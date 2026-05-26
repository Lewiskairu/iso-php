<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Services\MailService;
use App\Services\TokenService;
use App\Repositories\UserRepository;

final class AuthController extends Controller
{
    private MailService $mail;
    private TokenService $tokens;
    private UserRepository $userRepo;

    public function __construct(\App\Core\Session $session)
    {
        parent::__construct($session);
        $this->mail = new MailService();
        $this->tokens = new TokenService();
        $this->userRepo = new UserRepository();
    }

    public function showLogin(): void
    {
        $this->view('auth/login', [
            'title' => 'Login',
            'error' => $this->session->consumeFlash('error'),
            'success' => $this->session->consumeFlash('success'),
        ]);
    }

    public function showSignup(): void
    {
        $this->view('auth/signup', [
            'title' => 'Create account',
            'error' => $this->session->consumeFlash('error'),
        ]);
    }

    public function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->session->flash('error', 'Enter your email and password.');
            redirect('/login');
        }

        $user = (new AuthService())->attempt($email, $password);
        if (!$user) {
            $this->session->flash('error', 'Invalid email or password.');
            redirect('/login');
        }

        if (empty($user['is_verified'])) {
            $this->session->flash('error', 'Please verify your email address before logging in.');
            redirect('/login');
        }

        $this->session->put((string) config('auth.session_key'), $user);
        redirect('/dashboard');
    }

    public function signup(): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if ($name === '') $errors['name'] = 'Name is required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email.';
        if (strlen($password) < 8) $errors['password'] = 'Min 8 chars.';

        if ($errors) {
            $this->session->flash('error', 'Please fix the errors below.');
            redirect('/signup');
        }

        $user = (new AuthService())->register($name, $email, $password);
        if (!$user) {
            $this->session->flash('error', 'Email already in use.');
            redirect('/signup');
        }

        // Generate verification token
        $token = $this->tokens->generate(['id' => $user['id']], 86400 * 2); // 2 days
        $link = url('/verify-email?token=' . $token);

        $this->mail->sendWelcome($user['email'], $user['name'], $link);

        $this->session->flash('success', 'Account created! Please check your email to verify your registration.');
        redirect('/login');
    }

    public function verifyEmail(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        $payload = $this->tokens->verify($token);

        if (!$payload || !isset($payload['id'])) {
            $this->session->flash('error', 'Invalid or expired verification link.');
            redirect('/login');
        }

        $this->userRepo->verifyEmail($payload['id']);
        $this->session->flash('success', 'Email verified successfully! You can now log in.');
        redirect('/login');
    }

    public function showForgotPassword(): void
    {
        $this->view('auth/forgot_password', [
            'title' => 'Forgot Password',
            'error' => $this->session->consumeFlash('error'),
            'success' => $this->session->consumeFlash('success'),
        ]);
    }

    public function forgotPassword(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $user = $this->userRepo->findByEmail($email);

        if ($user) {
            $token = $this->tokens->generate(['id' => $user['id'], 'action' => 'pw_reset'], 3600);
            $link = url('/reset-password?token=' . $token);
            $this->mail->sendPasswordReset($user['email'], $link);
        }

        // Always show success to prevent email enumeration
        $this->session->flash('success', 'If an account exists for that email, a reset link has been sent.');
        redirect('/forgot-password');
    }

    public function showResetPassword(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        $this->view('auth/reset_password', [
            'title' => 'Reset Password',
            'token' => $token,
            'error' => $this->session->consumeFlash('error'),
        ]);
    }

    public function resetPassword(): void
    {
        $token = (string) ($_POST['token'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');

        if ($password !== $confirm) {
            $this->session->flash('error', 'Passwords do not match.');
            redirect('/reset-password?token=' . $token);
        }

        $payload = $this->tokens->verify($token);
        if (!$payload || ($payload['action'] ?? '') !== 'pw_reset') {
            $this->session->flash('error', 'Invalid or expired reset link.');
            redirect('/forgot-password');
        }

        $this->userRepo->updatePassword($payload['id'], password_hash($password, PASSWORD_BCRYPT));
        $this->session->flash('success', 'Password updated successfully. You can now log in.');
        redirect('/login');
    }

    public function logout(): void
    {
        $this->session->remove((string) config('auth.session_key'));
        redirect('/login');
    }
}
