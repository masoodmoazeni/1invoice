<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.turnstile.secret_key');

        if (empty($secret)) {
            return;
        }

        if (empty($value) || ! is_string($value)) {
            $fail('CAPTCHA verification is required.');
            return;
        }

        try {
            $response = Http::asForm()->timeout(10)->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret' => $secret,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]
            );

            if (! $response->ok()) {
                Log::warning('Turnstile siteverify request failed', [
                    'status' => $response->status(),
                ]);
                $fail('CAPTCHA verification failed. Please try again.');
                return;
            }

            $body = $response->json();

            if (! ($body['success'] ?? false)) {
                Log::info('Turnstile verification failed', [
                    'errors' => $body['error-codes'] ?? [],
                ]);
                $fail('CAPTCHA verification failed. Please try again.');
            }
        } catch (\Throwable $e) {
            Log::error('Turnstile verification error: ' . $e->getMessage());
            $fail('CAPTCHA verification failed. Please try again.');
        }
    }
}
