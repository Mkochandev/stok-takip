<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadApiController extends Controller
{
    /**
     * Store new lead coming from gaziustam-website
     */
    public function store(Request $request)
    {
        $secretKey = env('WEBSITE_API_SECRET', 'gaziustam_secret_2026');
        $providedSecret = $request->header('X-Api-Secret') ?? $request->input('api_secret');

        if ($providedSecret !== $secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized API request.'
            ], 401);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:contact,trial',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'package_name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:2000',
            'ip_address' => 'nullable|string|max:100',
        ]);

        try {
            $record = ContactRequest::create([
                'type' => $validated['type'],
                'name' => $validated['name'],
                'company_name' => $validated['company_name'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'package_name' => $validated['package_name'] ?? ($validated['type'] === 'trial' ? '14 Gün Ücretsiz Deneme' : 'Standart Paket'),
                'message' => $validated['message'] ?? null,
                'status' => 'yeni',
                'ip_address' => $validated['ip_address'] ?? $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Talep başarıyla kaydedildi.',
                'lead_id' => $record->id
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Lead API Save Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Talep kaydedilirken veritabanı hatası oluştu.'
            ], 500);
        }
    }
}
