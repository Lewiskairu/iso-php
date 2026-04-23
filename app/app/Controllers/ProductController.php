<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ProductRepository;

final class ProductController extends Controller
{
    public function index(): void
    {
        $repository = new ProductRepository();
        $this->view('products/index', [
            'title' => 'Products',
            'products' => $repository->getActiveProducts(),
            'categories' => $repository->categories(),
        ]);
    }
}
