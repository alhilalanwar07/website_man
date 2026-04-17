<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.guest')]
#[Title('Login - Admin Panel')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        $throttleKey = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        RateLimiter::clear($throttleKey);

        session()->regenerate();

        $user = Auth::user();

        if ($user?->hasRole('ppdb-admin') && ! $user->hasRole('admin')) {
            $this->redirect(route('admin.ppdb.dashboard'), navigate: true);
            return;
        }

        $this->redirect(route('admin.dashboard'), navigate: true);
    }

    protected function throttleKey(): string
    {
        $email = mb_strtolower(trim($this->email));
        $ipAddress = request()->ip() ?? 'unknown';

        return $email . '|' . $ipAddress;
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
