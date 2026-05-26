<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\CertificationRepository;
use App\Repositories\NominationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\MailService;
use App\Core\Database;

final class PublicActionController extends Controller
{
    private MailService $mail;

    public function __construct(\App\Core\Session $session)
    {
        parent::__construct($session);
        $this->mail = new MailService();
    }

    public function nominate(): void
    {
        $this->view('public/nominate', [
            'title' => 'Nominate',
            'flash' => $this->session->consumeFlash('success'),
        ]);
    }

    public function saveNomination(): void
    {
        $payload = [
            'nominatorName' => trim((string) ($_POST['nominatorName'] ?? '')),
            'nominatorEmail' => trim((string) ($_POST['nominatorEmail'] ?? '')),
            'nomineeName' => trim((string) ($_POST['nomineeName'] ?? '')),
            'nomineeEmail' => trim((string) ($_POST['nomineeEmail'] ?? '')),
            'nominationType' => trim((string) ($_POST['nominationType'] ?? 'ORGANISATION')),
            'reason' => trim((string) ($_POST['reason'] ?? '')),
        ];

        (new NominationRepository())->create($payload);

        // Send Emails
        $this->mail->sendNominationAlert($payload['nominatorEmail'], $payload['nominatorName'], $payload['nomineeName'], $payload['nominationType'], 'nominator');
        
        if (!empty($payload['nomineeEmail'])) {
            $this->mail->sendNominationAlert($payload['nomineeEmail'], $payload['nominatorName'], $payload['nomineeName'], $payload['nominationType'], 'nominee');
        }

        // Alert organization (admin)
        $adminEmail = Database::query('SELECT value FROM site_settings WHERE `key` = "smtp_user" LIMIT 1')->fetchColumn();
        if ($adminEmail) {
            $this->mail->sendNominationAlert((string)$adminEmail, $payload['nominatorName'], $payload['nomineeName'], $payload['nominationType'], 'organization');
        }

        $this->session->flash('success', 'Nomination submitted successfully! Emails have been sent.');
        redirect('/nominate');
    }

    public function certification(): void
    {
        $user = $this->session->get((string) config('auth.session_key'));
        $repository = new CertificationRepository();
        $this->view('public/certification', [
            'title' => 'Certification Request',
            'flash' => $this->session->consumeFlash('success'),
            'requests' => $repository->byUser($user['id'] ?? null),
        ]);
    }

    public function saveCertification(): void
    {
        $user = $this->session->get((string) config('auth.session_key'));
        (new CertificationRepository())->create([
            'companyName' => trim((string) ($_POST['companyName'] ?? '')),
            'contactName' => trim((string) ($_POST['contactName'] ?? '')),
            'contactEmail' => trim((string) ($_POST['contactEmail'] ?? '')),
            'contactPhone' => trim((string) ($_POST['contactPhone'] ?? '')),
            'companySize' => trim((string) ($_POST['companySize'] ?? '')),
            'currentStatus' => trim((string) ($_POST['currentStatus'] ?? '')),
            'requirements' => trim((string) ($_POST['requirements'] ?? '')),
            'userId' => $user['id'] ?? null,
        ]);

        $this->session->flash('success', 'Certification request submitted.');
        redirect('/certification/request');
    }

    public function product(): void
    {
        $id = (string) ($_GET['id'] ?? '');
        $repository = new ProductRepository();
        $product = $repository->findActiveProduct($id);
        if (!$product) {
            http_response_code(404);
            exit('Product not found');
        }

        $gallery = $repository->productImages($id);
        if (!$gallery && !empty($product['imageurl'])) {
            $gallery[] = ['image_url' => $product['imageurl'], 'sort_order' => 0];
        }

        $this->view('products/show', [
            'title' => $product['name'],
            'product' => $product,
            'gallery' => $gallery,
            'recommendations' => $repository->recommendations($id),
            'flash' => $this->session->consumeFlash('success'),
            'error' => $this->session->consumeFlash('error'),
        ]);
    }

    public function cart(): void
    {
        [$products, $total, $cartCount] = $this->cartSnapshot();

        $this->view('public/cart', [
            'title' => 'Cart',
            'items' => $products,
            'total' => $total,
            'cartCount' => $cartCount,
            'flash' => $this->session->consumeFlash('success'),
            'error' => $this->session->consumeFlash('error'),
        ]);
    }

    public function addToCart(): void
    {
        $id = (string) ($_POST['product_id'] ?? '');
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $product = $id !== '' ? (new ProductRepository())->findActiveProduct($id) : null;

        if (!$product) {
            $this->session->flash('error', 'Product not found.');
            redirect('/products');
        }

        $availableStock = (int) ($product['stock'] ?? 0);
        if ($availableStock <= 0) {
            $this->session->flash('error', 'This product is currently out of stock.');
            redirect('/products/show?id=' . urlencode($id));
        }
        $existing = (int) ($_SESSION['cart'][$id] ?? 0);
        if ($availableStock > 0 && ($existing + $quantity) > $availableStock) {
            $this->session->flash('error', 'Requested quantity exceeds available stock.');
            redirect('/products/show?id=' . urlencode($id));
        }

        $_SESSION['cart'][$id] = $existing + $quantity;
        $this->session->flash('success', 'Product added to cart.');
        redirect('/products/show?id=' . urlencode($id));
    }

    public function updateCart(): void
    {
        $quantities = $_POST['quantities'] ?? [];
        if (!is_array($quantities)) {
            redirect('/cart');
        }

        $repo = new ProductRepository();
        foreach ($quantities as $productId => $quantity) {
            $productId = (string) $productId;
            $quantity = max(0, (int) $quantity);
            if ($quantity === 0) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }

            $product = $repo->findActiveProduct($productId);
            if (!$product) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }

            $stock = (int) ($product['stock'] ?? 0);
            $_SESSION['cart'][$productId] = $stock > 0 ? min($quantity, $stock) : $quantity;
        }

        $this->session->flash('success', 'Cart updated.');
        redirect('/cart');
    }

    public function removeFromCart(): void
    {
        $productId = (string) ($_POST['product_id'] ?? '');
        if ($productId !== '') {
            unset($_SESSION['cart'][$productId]);
        }

        $this->session->flash('success', 'Item removed from cart.');
        redirect('/cart');
    }

    public function clearCart(): void
    {
        unset($_SESSION['cart']);
        $this->session->flash('success', 'Cart cleared.');
        redirect('/cart');
    }

    public function checkout(): void
    {
        $user = $this->requireAuth();
        $orders = (new OrderRepository())->byUser($user['id']);
        [$items, $total, $cartCount] = $this->cartSnapshot();
        $this->view('public/checkout', [
            'title' => 'Checkout',
            'orders' => $orders,
            'items' => $items,
            'total' => $total,
            'cartCount' => $cartCount,
            'flash' => $this->session->consumeFlash('success'),
            'error' => $this->session->consumeFlash('error'),
        ]);
    }

    public function submitCheckout(): void
    {
        $user = $this->requireAuth();
        [$items, $total] = $this->cartSnapshot();

        if ($items === []) {
            $this->session->flash('error', 'Your cart is empty.');
            redirect('/checkout');
        }

        $lineItems = [];
        $repo = new ProductRepository();
        foreach ($items as $item) {
            $fresh = $repo->findActiveProduct((string) $item['id']);
            if (!$fresh) {
                $this->session->flash('error', 'One of the products is no longer available.');
                redirect('/checkout');
            }

            $lineItems[] = [
                'product_id' => $fresh['id'],
                'quantity' => (int) $item['quantity'],
                'price' => (float) $fresh['price'],
            ];
        }

        $currency = (string) ($items[0]['currency'] ?? 'USD');
        $orderId = (new OrderRepository())->createOrder($user['id'], $lineItems, $currency);

        // Send Email
        $this->mail->sendOrderConfirmation($user['email'], $orderId, (string)$total, $currency);

        unset($_SESSION['cart']);
        $this->session->flash('success', 'Order ' . $orderId . ' placed! Confirmation email sent.');
        redirect('/orders/track?id=' . $orderId);
    }

    public function order(): void
    {
        $user = $this->requireAuth();
        $orderId = (string) ($_GET['id'] ?? '');
        $order = (new OrderRepository())->findForUser($orderId, $user['id']);
        if (!$order) {
            http_response_code(404);
            exit('Order not found');
        }

        $this->view('public/order', [
            'title' => 'Order ' . $orderId,
            'order' => $order,
        ]);
    }

    public function trackOrder(): void
    {
        $user = $this->requireAuth();
        $orderId = (string) ($_GET['id'] ?? '');
        $order = (new OrderRepository())->findForUser($orderId, $user['id']);

        $this->view('public/track', [
            'title' => 'Track Order',
            'order' => $order,
            'orderId' => $orderId,
        ]);
    }

    private function cartSnapshot(): array
    {
        $cart = $_SESSION['cart'] ?? [];
        $products = [];
        $total = 0.0;
        $repo = new ProductRepository();
        foreach ($cart as $productId => $qty) {
            $quantity = max(1, (int) $qty);
            $product = $repo->findActiveProduct((string) $productId);
            if ($product) {
                $product['quantity'] = $quantity;
                $product['line_total'] = (float) $product['price'] * $quantity;
                $total += $product['line_total'];
                $products[] = $product;
            } else {
                unset($_SESSION['cart'][$productId]);
            }
        }

        return [$products, $total, array_sum($_SESSION['cart'] ?? [])];
    }
}
