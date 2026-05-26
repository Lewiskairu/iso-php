<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\UserRepository;

final class OAuthController extends Controller
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct(\App\Core\Session $session)
    {
        parent::__construct($session);
        $this->clientId = (string) $this->getSetting('google_client_id');
        $this->clientSecret = (string) $this->getSetting('google_client_secret');
        $this->redirectUri = (string) url('/auth/google/callback');
    }

    public function loginWithGoogle(): void
    {
        if ($this->clientId === '') {
            $this->session->flash('error', 'Google Login is not configured.');
            redirect('/login');
        }

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];

        $url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($params);
        redirect($url);
    }

    public function handleGoogleCallback(): void
    {
        $code = (string) ($_GET['code'] ?? '');
        if ($code === '') {
            $this->session->flash('error', 'Google authentication failed.');
            redirect('/login');
        }

        // Exchange code for token
        $tokenData = $this->exchangeCodeForToken($code);
        if (!$tokenData || !isset($tokenData['access_token'])) {
            $this->session->flash('error', 'Failed to retrieve access token from Google.');
            redirect('/login');
        }

        // Get user info
        $googleUser = $this->getGoogleUserInfo($tokenData['access_token']);
        if (!$googleUser || !isset($googleUser['email'])) {
            $this->session->flash('error', 'Failed to retrieve user information from Google.');
            redirect('/login');
        }

        // Find or create user
        $userRepo = new UserRepository();
        $user = $userRepo->findByEmail($googleUser['email']);

        if (!$user) {
            // Register new OAuth user
            $id = bin2hex(random_bytes(16));
            $userRepo->create($id, $googleUser['email'], '', $googleUser['name'] ?? 'Google User');
            $userRepo->verifyEmail($id); // Google emails are pre-verified
            $user = $userRepo->findById($id);
        }

        $this->session->put((string) config('auth.session_key'), $user);
        redirect('/dashboard');
    }

    private function exchangeCodeForToken(string $code): ?array
    {
        $params = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init("https://oauth2.googleapis.com/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode((string) $response, true) : null;
    }

    private function getGoogleUserInfo(string $accessToken): ?array
    {
        $ch = curl_init("https://www.googleapis.com/oauth2/v3/userinfo");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$accessToken}"]);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode((string) $response, true) : null;
    }

    private function getSetting(string $key): ?string
    {
        return Database::query('SELECT value FROM site_settings WHERE `key` = :key LIMIT 1', ['key' => $key])->fetchColumn() ?: '';
    }
}
