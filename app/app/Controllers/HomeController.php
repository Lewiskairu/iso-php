<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AssessmentRepository;
use App\Repositories\ContentRepository;
use App\Repositories\ProductRepository;

final class HomeController extends Controller
{
    public function index(): void
    {
        $content = new ContentRepository();
        $standards = (new AssessmentRepository())->getStandards();
        $productRepository = new ProductRepository();
        $products = array_slice($productRepository->getActiveProducts(), 0, 4);
        $hero = $content->heroSettings();
        $about = $content->about();
        $heroSlides = $content->heroSlides();

        $this->view('home/index', [
            'title'      => $hero['hero_title'] ?? 'ISO Compliance Hub',
            'standards'  => $standards,
            'products'   => $products,
            'partners'   => $content->partners(),
            'hero'       => $hero,
            'about'      => $about,
            'heroSlides' => $heroSlides,
        ]);
    }
}
