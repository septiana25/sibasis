<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AiChatSsoController extends Controller
{
    /**
     * Panjang hidup token SSO dalam menit.
     */
    private const TOKEN_TTL_MINUTES = 2;

    /**
     * Redirect anggota yang sudah login ke Aplikasi Chat AI dengan membawa token SSO sekali pakai.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $member = Auth::guard('member')->user();
        $query = (string) $request->query('q', '');

        $token = bin2hex(random_bytes(32));

        Cache::put('sso_token_' . $token, $member, now()->addMinutes(self::TOKEN_TTL_MINUTES));

        $chatAppUrl = rtrim((string) env('CHAT_APP_URL'), '/');

        return redirect()->away(
            $chatAppUrl . '/auth/sso?token=' . $token . '&q=' . urlencode($query)
        );
    }

    /**
     * Verifikasi token SSO dari Aplikasi Chat AI. Token hanya bisa dipakai sekali (anti replay attack).
     */
    public function verify(Request $request): JsonResponse
    {
        $token = (string) $request->query('token', '');
        $cacheKey = 'sso_token_' . $token;

        $member = $token !== '' ? Cache::get($cacheKey) : null;

        if (! $member) {
            return response()->json([
                'valid' => false,
            ], 401);
        }

        // Hapus cache segera setelah dipakai agar token tidak bisa dipakai ulang
        Cache::forget($cacheKey);

        return response()->json([
            'valid' => true,
            'user' => [
                'nim' => $member->member_id,
                'name' => $member->member_name,
                'email' => $member->member_email ?? null,
            ],
        ]);
    }
}
