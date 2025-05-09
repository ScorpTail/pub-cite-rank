<?php

namespace App\Services\AuthServices;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthResetPasswordService
{
    public function sendResetLink(string $email)
    {
        DB::transaction(function () use ($email) {
            PasswordResetToken::where('email', $email)->delete();

            $token = $this->generateToken();

            $this->createPasswordResetTokenRecord($email, $token);

            return $this->sendMail($email, $token);
        });

        return true;
    }

    public function resetPassword(array $resetData)
    {
        $email = data_get($resetData, 'email');

        DB::transaction(function () use ($resetData, $email) {
            User::query()
                ->where('email', $email)
                ->update(['password' => bcrypt(data_get($resetData, 'password'))]);

            PasswordResetToken::where('email', $email)
                ->delete();
        });

        return true;
    }

    private function generateToken()
    {
        return Str::random(60);
    }

    private function createPasswordResetTokenRecord(string $email, string $token)
    {
        return PasswordResetToken::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);
    }

    private function sendMail(string $email, string $token)
    {
        return Mail::raw("Перейдіть за посиланням для відновлення паролю: " . route('auth.reset-password', ['token' => $token]), function ($message) use ($email) {
            $message->to($email)
                ->subject('Відновлення паролю');
        });

        // Mail::send('emails.password_reset', ['token' => $token, 'email' => $email], function ($message) use ($email) {
        //     $message->to($email)->subject('Відновлення паролю');
        // });
    }
}