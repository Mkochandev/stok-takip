<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminContactRequestController extends Controller
{
    /**
     * Display listing of contact and trial requests.
     */
    public function index(Request $request)
    {
        $query = ContactRequest::latest();

        // Type filter
        if ($request->has('type') && in_array($request->type, ['contact', 'trial'])) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->has('status') && in_array($request->status, ['yeni', 'arandi', 'beklemede', 'uye_yapildi', 'iptal'])) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total'       => ContactRequest::count(),
            'yeni'        => ContactRequest::where('status', 'yeni')->count(),
            'trial'       => ContactRequest::where('type', 'trial')->count(),
            'contact'     => ContactRequest::where('type', 'contact')->count(),
            'uye_yapildi' => ContactRequest::where('status', 'uye_yapildi')->count(),
        ];

        return view('admin.requests.index', compact('requests', 'stats'));
    }

    /**
     * Update request status.
     */
    public function updateStatus(Request $request, ContactRequest $contactRequest)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:yeni,arandi,beklemede,uye_yapildi,iptal',
        ]);

        $contactRequest->update([
            'status' => $validated['status']
        ]);

        return back()->with('success', 'Talep durumu başarıyla güncellendi.');
    }

    /**
     * Delete contact request.
     */
    public function destroy(ContactRequest $contactRequest)
    {
        $contactRequest->delete();

        return back()->with('success', 'Müşteri talebi silindi.');
    }

    /**
     * Convert lead to active user account (Create User).
     */
    public function convertToUser(ContactRequest $contactRequest)
    {
        // Check if email already exists
        $existingUser = User::where('email', $contactRequest->email)->first();
        if ($existingUser) {
            return back()->with('error', 'Bu e-posta adresiyle zaten kayıtlı bir kullanıcı var: ' . $existingUser->name);
        }

        $tempPassword = Str::random(8);

        $user = User::create([
            'name'       => $contactRequest->name,
            'email'      => $contactRequest->email,
            'phone'      => $contactRequest->phone,
            'password'   => Hash::make($tempPassword),
            'is_admin'   => false,
            'expires_at' => now()->addDays(14), // 14 günlük deneme süresi tanımla
        ]);

        $contactRequest->update(['status' => 'uye_yapildi']);

        return back()->with('success', "Kullanıcı hesabı oluşturuldu ve 14 gün ücretsiz deneme tanımlandı! Geçici Şifre: {$tempPassword}");
    }
}
