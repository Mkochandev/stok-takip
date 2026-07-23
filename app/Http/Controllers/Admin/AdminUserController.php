<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminUserController extends Controller
{
    /**
     * Üyeleri Listele
     */
    public function index(Request $request)
    {
        $query = $request->get('q');

        $users = User::when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->withCount(['ustalar', 'isler', 'devamKayitlari', 'gelirler', 'giderler', 'odemeler'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'query'));
    }

    /**
     * Yeni Üye Oluşturma Formu
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Yeni Üye Kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:6',
            'is_admin'   => 'nullable|boolean',
            'expires_at' => 'nullable|date',
            'preset_süre'=> 'nullable|string',
        ]);

        // Preset hızlı süre seçimi
        $expiresAt = null;
        if (!empty($validated['preset_süre'])) {
            $expiresAt = match ($validated['preset_süre']) {
                '14_gun' => now()->addDays(14),
                '1_ay'   => now()->addMonth(),
                '3_ay'   => now()->addMonths(3),
                '6_ay'   => now()->addMonths(6),
                '1_yil'  => now()->addYear(),
                'suresiz'=> null,
                default  => $validated['expires_at'] ? Carbon::parse($validated['expires_at']) : null,
            };
        } elseif (!empty($validated['expires_at'])) {
            $expiresAt = Carbon::parse($validated['expires_at']);
        }

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'is_admin'   => (bool) ($request->has('is_admin') && $request->is_admin),
            'expires_at' => $expiresAt,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', $user->name . ' üyelik kaydı başarıyla oluşturuldu.');
    }

    /**
     * Üye Düzenleme Formu
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Üye Güncelle
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'   => 'nullable|string|min:6',
            'is_admin'   => 'nullable|boolean',
            'expires_at' => 'nullable|date',
        ]);

        $data = [
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'is_admin'   => (bool) ($request->has('is_admin') && $request->is_admin),
            'expires_at' => !empty($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', $user->name . ' üyelik bilgileri güncellendi.');
    }

    /**
     * Hızlı Üyelik Süresi Uzatma (AJAX veya POST)
     */
    public function extend(Request $request, User $user)
    {
        $request->validate([
            'süre' => 'required|in:1_ay,3_ay,6_ay,1_yil,suresiz',
        ]);

        $sureMap = [
            '1_ay'   => 1,
            '3_ay'   => 3,
            '6_ay'   => 6,
            '1_yil'  => 12,
        ];

        if ($request->süre === 'suresiz') {
            $user->update(['expires_at' => null]);
            $mesaj = $user->name . ' hesabının süresi SÜRESİZ olarak güncellendi.';
        } else {
            $aylar = $sureMap[$request->süre] ?? 1;
            // Eğer süresi geçmişse bugünden itibaren uzat, geçmemişse mevcut tarihe ekle
            $baslangic = ($user->expires_at && $user->expires_at->isFuture()) ? $user->expires_at : now();
            $yeniTarih = $baslangic->copy()->addMonths($aylar);

            $user->update(['expires_at' => $yeniTarih]);
            $mesaj = $user->name . ' üyeliği ' . $aylar . ' ay uzatıldı. Yeni Son Tarih: ' . $yeniTarih->format('d.m.Y');
        }

        return back()->with('success', $mesaj);
    }

    /**
     * Üye Verilerini Yedekle / İndir (JSON Export)
     */
    public function backup(User $user)
    {
        // Global scope'ları bypass edip sadece bu kullanıcının verilerini çekiyoruz
        $data = [
            'kullanici'       => [
                'id'         => $user->id,
                'ad_soyad'   => $user->name,
                'email'      => $user->email,
                'kayit_tarihi'=> $user->created_at?->toIso8601String(),
                'expires_at' => $user->expires_at?->toIso8601String(),
            ],
            'yedek_tarihi'    => now()->toIso8601String(),
            'ustalar'         => $user->ustalar()->withoutGlobalScopes()->get(),
            'isler'           => $user->isler()->withoutGlobalScopes()->get(),
            'devam_kayitlari' => $user->devamKayitlari()->withoutGlobalScopes()->get(),
            'gelirler'        => $user->gelirler()->withoutGlobalScopes()->get(),
            'giderler'        => $user->giderler()->withoutGlobalScopes()->get(),
            'odemeler'        => $user->odemeler()->withoutGlobalScopes()->get(),
        ];

        $jsonFileName = 'gaziustam_yedek_' . preg_replace('/[^a-z0-9]/', '_', strtolower($user->name)) . '_' . now()->format('Y-m-d_H-i') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $jsonFileName, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Üyeyi Sil
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kendi admin hesabınızı silemezsiniz!');
        }

        $ad = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', $ad . ' hesabı ve tüm verileri silindi.');
    }
}
