<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;

final class ProfileController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();
        $profile = (new UserRepository())->findById($user['id']);

        $this->view('profile/index', [
            'title' => 'Profile',
            'profile' => $profile,
            'success' => $this->session->consumeFlash('success'),
        ]);
    }

    public function update(): void
    {
        $user = $this->requireAuth();
        $name = trim((string) ($_POST['name'] ?? ''));

        if ($name === '') {
            $this->flashFormState(['name' => 'Name is required.'], ['name' => $name]);
            redirect('/profile');
        }

        $imagePath = $this->storeImageUpload($_FILES['image'] ?? null);
        (new UserRepository())->updateProfile($user['id'], $name, $imagePath);

        $user['name'] = $name;
        if ($imagePath !== null) {
            $user['image'] = $imagePath;
        }

        $this->session->put((string) config('auth.session_key'), $user);
        $this->session->flash('success', 'Profile updated.');
        redirect('/profile');
    }

    private function storeImageUpload(mixed $file): ?string
    {
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return null;
        }

        $mime = mime_content_type((string) $file['tmp_name']) ?: '';
        if (!str_starts_with($mime, 'image/')) {
            return null;
        }

        $extension = pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION) ?: 'jpg';
        $directory = BASE_PATH . '/public/uploads/profiles';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $filename = time() . '-' . bin2hex(random_bytes(6)) . '.' . strtolower($extension);
        $target = $directory . '/' . $filename;
        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
            return null;
        }

        return '/uploads/profiles/' . $filename;
    }
}
