<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', [
            'title' => 'Login',
            'error' => $this->session->consumeFlash('error'),
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
            $this->flashFormState(
                [
                    'email' => $email === '' ? 'Email is required.' : null,
                    'password' => $password === '' ? 'Password is required.' : null,
                ],
                ['email' => $email]
            );
            $this->session->flash('error', 'Enter your email and password.');
            redirect('/login');
        }

        $user = (new AuthService())->attempt($email, $password);
        if (!$user) {
            $this->flashFormState(['email' => 'Check your login details.'], ['email' => $email]);
            $this->session->flash('error', 'Invalid email or password.');
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

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if ($errors) {
            $this->flashFormState($errors, ['name' => $name, 'email' => $email]);
            $this->session->flash('error', 'Name, email, and password are required.');
            redirect('/signup');
        }

        $user = (new AuthService())->register($name, $email, $password);
        if (!$user) {
            $this->flashFormState(['email' => 'This email address is already in use.'], ['name' => $name, 'email' => $email]);
            $this->session->flash('error', 'This email is already in use.');
            redirect('/signup');
        }

        $this->session->put((string) config('auth.session_key'), $user);
        redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->session->remove((string) config('auth.session_key'));
        redirect('/login');
    }
}
