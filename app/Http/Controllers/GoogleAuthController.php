<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and log them in.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            // Using stateless() to prevent session state mismatch issues in custom environments
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find or create the user
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // Update tokens
                $user->update([
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
                ]);
            } else {
                // Check if user exists with the same email
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Link the Google account
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                    ]);
                } else {
                    // Create a new user
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'password' => Hash::make(Str::random(24)),
                    ]);
                }
            }

            // Log the user in
            Auth::login($user, true);

            return redirect()->intended('/')->with('success', '¡Conexión segura establecida con éxito usando Google!');
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Hubo un problema al autenticar con Google: ' . $e->getMessage()
            ]);
        }
    }
}
