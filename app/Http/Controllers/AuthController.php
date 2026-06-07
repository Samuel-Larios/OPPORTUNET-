<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Support\SubmissionGuard;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function createLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        SubmissionGuard::ensureSafeRequest($request, ['email'], true);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => __('admin.auth.invalid_credentials')])
                ->onlyInput(['email', 'redirect_to']);
        }

        $request->session()->regenerate();

        if (! $request->user()->actif) {
            Auth::logout();

            return back()
                ->withErrors(['email' => __('admin.auth.invalid_credentials')])
                ->onlyInput(['email', 'redirect_to']);
        }

        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->to($this->resolveRedirectTarget($request, route($request->user()->dashboardRouteName())));
    }

    public function createRegister(): View
    {
        return view('auth.register');
    }

    public function createUserRegister(): View
    {
        return view('auth.register-user');
    }

    public function register(Request $request): RedirectResponse
    {
        SubmissionGuard::ensureSafeRequest($request, ['prenom', 'nom', 'email', 'pays'], true);

        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'pays' => ['nullable', 'string', 'max:80'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $roleName = $request->routeIs('register.store', 'register.company.store')
            ? 'entreprise'
            : 'user';

        $userRoleId = Role::query()->where('nom', $roleName)->value('id');

        $user = User::query()->create([
            'role_id' => $userRoleId,
            'prenom' => $data['prenom'],
            'nom' => $data['nom'],
            'name' => trim($data['prenom'] . ' ' . $data['nom']),
            'email' => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'pays' => $data['pays'] ?? null,
            'password' => Hash::make($data['password']),
            'actif' => true,
            'newsletter' => true,
            'langue' => app()->getLocale(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if (! $this->dispatchVerificationEmail($user)) {
            return redirect()
                ->route('verification.notice')
                ->withErrors(['verification_email' => __('admin.auth.verify_send_failed')]);
        }

        return redirect()->route('verification.notice');
    }

    public function createVerifyEmail(): View|RedirectResponse
    {
        if (auth()->user()?->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    public function sendVerificationEmail(Request $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $user = $request->user();

        if ($user !== null && ! $this->dispatchVerificationEmail($user)) {
            return back()->withErrors(['verification_email' => __('admin.auth.verify_send_failed')]);
        }

        return back()->with('status', 'verification-link-sent');
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()
            ->route('dashboard')
            ->with('status', 'email-verified');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function resolveRedirectTarget(Request $request, string $fallback): string
    {
        $redirectTo = $request->input('redirect_to');

        if (is_string($redirectTo) && $redirectTo !== '') {
            $parts = parse_url($redirectTo);
            $host = $parts['host'] ?? null;
            $path = $parts['path'] ?? null;

            if (($host === null || $host === $request->getHost()) && is_string($path) && str_starts_with($path, '/')) {
                $target = $path;

                if (! empty($parts['query'])) {
                    $target .= '?' . $parts['query'];
                }

                if (! empty($parts['fragment'])) {
                    $target .= '#' . $parts['fragment'];
                }

                return $target;
            }
        }

        return $fallback;
    }

    private function dispatchVerificationEmail(User $user): bool
    {
        try {
            $user->sendEmailVerificationNotification();

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
