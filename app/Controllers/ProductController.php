<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;

class ProductController extends Controller {
    public function index(): void {
        $productModel = new Product();
        $categoryModel = new Category();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $filters = [
            'category' => $_GET['category'] ?? null,
            'search' => $_GET['search'] ?? null,
            'min_price' => isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price' => isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'sort' => $_GET['sort'] ?? 'newest',
            'in_stock' => !empty($_GET['in_stock']),
            'featured' => !empty($_GET['featured']),
            'bestseller' => !empty($_GET['bestseller']),
        ];

        $products = $productModel->getFiltered($filters, $limit, $offset);
        $totalProducts = $productModel->countFiltered($filters);
        $totalPages = (int) ceil($totalProducts / $limit);

        $categories = $categoryModel->getAll(true);
        $currentCategory = null;
        if (!empty($filters['category'])) {
            $currentCategory = $categoryModel->findBySlug((string)$filters['category']);
        }

        $pageTitle = 'Buy Live Plants & Garden Supplies Online';
        if ($currentCategory) {
            $pageTitle = $currentCategory['name'] . ' - Online Nursery';
        } elseif (!empty($filters['search'])) {
            $pageTitle = 'Search Results for "' . htmlspecialchars($filters['search']) . '"';
        }

        $this->render('products/index', [
            'title' => $pageTitle,
            'products' => $products,
            'totalProducts' => $totalProducts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => $filters,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
        ]);
    }

    public function show(string $slug): void {
        $productModel = new Product();
        $reviewModel = new Review();

        $product = $productModel->findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Plant Not Found']);
            return;
        }

        $relatedProducts = $productModel->getRelated((int)$product['category_id'], (int)$product['id'], 4);
        $reviews = $reviewModel->getByProduct((int)($product['product_id'] ?? $product['id']));

        $this->render('products/show', [
            'title' => $product['name'] . ' | Buy Online India',
            'meta_description' => $product['short_description'] ?? $product['name'],
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews,
        ]);
    }
}
