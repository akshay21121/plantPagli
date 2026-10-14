<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\Review;
use App\Models\Product;

class ReviewController extends Controller {
    public function store(): void {
        $this->validateCSRF();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $productModel = new Product();
        $product = $productModel->findById($productId);

        if (!$product) {
            $this->redirect('/products', 'error', 'Product not found.');
            return;
        }

        $rules = [
            'rating' => 'required|numeric',
            'comment' => 'required|min:5|max:1000',
        ];

        if (!Auth::check()) {
            $rules['customer_name'] = 'required|min:2';
            $rules['customer_email'] = 'required|email';
        }

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            $this->redirect("/product/{$product['slug']}", 'error', 'Please provide a valid rating and review comment.');
            return;
        }

        $user = Auth::user();
        $reviewModel = new Review();

        $reviewModel->add([
            'product_id' => $productId,
            'user_id' => $user['id'] ?? null,
            'customer_name' => $user['name'] ?? trim($_POST['customer_name']),
            'customer_email' => $user['email'] ?? trim(strtolower($_POST['customer_email'])),
            'rating' => (int) $_POST['rating'],
            'title' => trim($_POST['title'] ?? ''),
            'comment' => trim($_POST['comment']),
        ]);

        $this->redirect("/product/{$product['slug']}", 'success', 'Thank you! Your plant review has been published.');
    }
}
