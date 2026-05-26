<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AdminCrudController;
use App\Controllers\AssessmentController;
use App\Controllers\AuthController;
use App\Controllers\ContentController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\OAuthController;
use App\Controllers\PartnerController;
use App\Controllers\ProductController;
use App\Controllers\ProfileController;
use App\Controllers\PublicActionController;
use App\Core\App;
use App\Core\Router;
use App\Core\Session;

require_once BASE_PATH . '/app/Support/helpers.php';

$session = new Session();
$router = new Router($session);

$router->get('/', [HomeController::class, 'index']);
 $router->get('/about', [ContentController::class, 'about']);
 $router->get('/terms', [ContentController::class, 'terms']);
 $router->get('/terms/show', [ContentController::class, 'term']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/signup', [AuthController::class, 'showSignup']);
$router->post('/signup', [AuthController::class, 'signup']);
$router->get('/verify-email', [AuthController::class, 'verifyEmail']);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/reset-password', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/auth/google', [OAuthController::class, 'loginWithGoogle']);
$router->get('/auth/google/callback', [OAuthController::class, 'handleGoogleCallback']);

$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/assessments', [AssessmentController::class, 'index']);
$router->get('/assessments/create', [AssessmentController::class, 'create']);
$router->post('/assessments', [AssessmentController::class, 'store']);
$router->get('/assessments/show', [AssessmentController::class, 'show']);
$router->get('/assessments/export', [AssessmentController::class, 'export']);
$router->post('/assessments/answers', [AssessmentController::class, 'saveAnswers']);

$router->get('/products', [ProductController::class, 'index']);
 $router->get('/products/show', [PublicActionController::class, 'product']);
 $router->post('/products/add-to-cart', [PublicActionController::class, 'addToCart']);

 $router->get('/nominate', [PublicActionController::class, 'nominate']);
 $router->post('/nominate', [PublicActionController::class, 'saveNomination']);
 $router->get('/certification/request', [PublicActionController::class, 'certification']);
 $router->post('/certification/request', [PublicActionController::class, 'saveCertification']);
 $router->get('/cart', [PublicActionController::class, 'cart']);
 $router->post('/cart/update', [PublicActionController::class, 'updateCart']);
 $router->post('/cart/remove', [PublicActionController::class, 'removeFromCart']);
 $router->post('/cart/clear', [PublicActionController::class, 'clearCart']);
 $router->get('/checkout', [PublicActionController::class, 'checkout']);
 $router->post('/checkout', [PublicActionController::class, 'submitCheckout']);
 $router->get('/orders/show', [PublicActionController::class, 'order']);
 $router->get('/orders/track', [PublicActionController::class, 'trackOrder']);

$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/settings', [AdminController::class, 'settings']);
 $router->get('/admin/manage', [AdminCrudController::class, 'index']);
 $router->get('/admin/form', [AdminCrudController::class, 'form']);
 $router->post('/admin/save', [AdminCrudController::class, 'save']);
 $router->post('/admin/delete', [AdminCrudController::class, 'delete']);
 $router->post('/admin/settings/normalize-category', [AdminCrudController::class, 'normalizeCategory']);
$router->get('/partner', [PartnerController::class, 'index']);
$router->get('/profile', [ProfileController::class, 'index']);
$router->post('/profile', [ProfileController::class, 'update']);

return new App($router, $session);
