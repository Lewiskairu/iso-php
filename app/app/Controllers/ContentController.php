<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ContentRepository;

final class ContentController extends Controller
{
    public function about(): void
    {
        $repo = new ContentRepository();
        $this->view('content/about', [
            'title' => 'About',
            'about' => $repo->about(),
            'partners' => $repo->partners(),
            'settings' => $repo->siteSettings(),
            'hero' => $repo->heroSettings(),
            'heroSlides' => $repo->heroSlides(),
        ]);
    }

    public function terms(): void
    {
        $repo = new ContentRepository();
        $this->view('content/terms', [
            'title' => 'Terms',
            'terms' => $repo->activeTerms(),
        ]);
    }

    public function term(): void
    {
        $repo = new ContentRepository();
        $term = $repo->term((int) ($_GET['id'] ?? 0));
        if (!$term) {
            http_response_code(404);
            exit('Term not found');
        }

        $this->view('content/term', [
            'title' => $term['title'],
            'term' => $term,
        ]);
    }
}
