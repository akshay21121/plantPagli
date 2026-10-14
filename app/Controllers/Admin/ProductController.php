<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Session;
use App\Models\Product;
use App\Models\Category;
use App\Services\UploadService;
use Exception;

class ProductController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $productModel = new Product();
        $categoryModel = new Category();

        $search = trim($_GET['search'] ?? '');
        $categoryId = !empty($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $products = $productModel->getAllForAdmin($search, $categoryId, $limit, $offset);
        $total = $productModel->countForAdmin($search, $categoryId);
        $totalPages = (int) ceil($total / $limit);
        $categories = $categoryModel->getAll(false);

        $this->render('admin/products/index', [
            'title' => 'Manage Products - PlantPuglu Admin',
            'products' => $products,
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'categoryId' => $categoryId,
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function create(): void {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll(false);

        $this->render('admin/products/create', [
            'title' => 'Add New Product - PlantPuglu Admin',
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function store(): void {
        $this->validateCSRF();

        $rules = [
            'category_id' => 'required|numeric',
            'name' => 'required|min:2|max:255',
            'sku' => 'required|unique:products,sku',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            Session::set('_admin_errors', $validator->errors());
            $this->redirect('/admin/products/create', 'error', 'Please fix the product validation errors.');
            return;
        }

        $productModel = new Product();

        try {
            $productId = $productModel->create($_POST);

            // Handle primary image upload
            if (!empty($_FILES['image']['name'])) {
                $imageFilename = UploadService::uploadImage($_FILES['image'], 'products');
                $productModel->addImage($productId, $imageFilename, true);
            }

            // Handle multiple alternate images
            if (!empty($_FILES['gallery_images']['name'][0])) {
                $totalFiles = count($_FILES['gallery_images']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    if (!empty($_FILES['gallery_images']['name'][$i])) {
                        $fileItem = [
                            'name' => $_FILES['gallery_images']['name'][$i],
                            'type' => $_FILES['gallery_images']['type'][$i],
                            'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i],
                            'error' => $_FILES['gallery_images']['error'][$i],
                            'size' => $_FILES['gallery_images']['size'][$i],
                        ];
                        $galleryFilename = UploadService::uploadImage($fileItem, 'products');
                        $productModel->addImage($productId, $galleryFilename, false);
                    }
                }
            }

            // Handle optional variants
            if (!empty($_POST['variant_names'])) {
                foreach ($_POST['variant_names'] as $idx => $vName) {
                    $vName = trim($vName);
                    if (!empty($vName)) {
                        $vPrice = (float) ($_POST['variant_prices'][$idx] ?? $_POST['price']);
                        $vStock = (int) ($_POST['variant_stocks'][$idx] ?? 10);
                        $vSku = trim($_POST['variant_skus'][$idx] ?? '');
                        $productModel->addVariant($productId, $vName, $vPrice, $vStock, $vSku ?: null);
                    }
                }
            }

            $this->redirect('/admin/products', 'success', "Product '{$_POST['name']}' created successfully!");
        } catch (Exception $e) {
            $this->redirect('/admin/products/create', 'error', 'Error saving product: ' . $e->getMessage());
        }
    }

    public function edit(string $id): void {
        $productModel = new Product();
        $product = $productModel->findById((int)$id);

        if (!$product) {
            $this->redirect('/admin/products', 'error', 'Product not found.');
            return;
        }

        $categoryModel = new Category();
        $categories = $categoryModel->getAll(false);

        $this->render('admin/products/edit', [
            'title' => 'Edit Product - ' . $product['name'],
            'product' => $product,
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function update(string $id): void {
        $this->validateCSRF();
        $productId = (int) $id;

        $rules = [
            'category_id' => 'required|numeric',
            'name' => 'required|min:2|max:255',
            'sku' => "required|unique:products,sku,{$productId}",
            'price' => 'required|numeric',
            'stock_quantity' => 'required|numeric',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            Session::set('_admin_errors', $validator->errors());
            $this->redirect("/admin/products/edit/{$productId}", 'error', 'Please correct form errors.');
            return;
        }

        $productModel = new Product();

        try {
            $productModel->update($productId, $_POST);

            // Handle new primary image upload
            if (!empty($_FILES['image']['name'])) {
                $imageFilename = UploadService::uploadImage($_FILES['image'], 'products');
                $productModel->addImage($productId, $imageFilename, true);
            }

            // Handle additional gallery images (2nd, 3rd, etc.)
            if (!empty($_FILES['gallery_images']['name'][0])) {
                $totalFiles = count($_FILES['gallery_images']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    if (!empty($_FILES['gallery_images']['name'][$i]) && $_FILES['gallery_images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                        $fileItem = [
                            'name' => $_FILES['gallery_images']['name'][$i],
                            'type' => $_FILES['gallery_images']['type'][$i],
                            'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i],
                            'error' => $_FILES['gallery_images']['error'][$i],
                            'size' => $_FILES['gallery_images']['size'][$i],
                        ];
                        $galleryFilename = UploadService::uploadImage($fileItem, 'products');
                        $productModel->addImage($productId, $galleryFilename, false);
                    }
                }
            }

            $this->redirect("/admin/products/edit/{$productId}", 'success', 'Product and photos updated successfully.');
        } catch (Exception $e) {
            $this->redirect("/admin/products/edit/{$productId}", 'error', 'Update error: ' . $e->getMessage());
        }
    }

    public function deleteImage(string $productId, string $imageId): void {
        $this->validateCSRF();
        $productModel = new Product();
        $productModel->deleteImage((int)$imageId, (int)$productId);
        $this->redirect("/admin/products/edit/{$productId}", 'success', 'Image removed successfully.');
    }

    public function setPrimaryImage(string $productId, string $imageId): void {
        $this->validateCSRF();
        $productModel = new Product();
        $productModel->setPrimaryImage((int)$imageId, (int)$productId);
        $this->redirect("/admin/products/edit/{$productId}", 'success', 'Primary display image updated.');
    }

    public function delete(string $id): void {
        $this->validateCSRF();
        $productModel = new Product();
        $productModel->delete((int)$id);
        $this->redirect('/admin/products', 'info', 'Product removed.');
    }

    public function exportCsv(): void {
        $productModel = new Product();
        $products = $productModel->getAllForAdmin('', null, 5000, 0);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=plantpuglu_products_' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'SKU', 'Name', 'Category', 'Price (INR)', 'Compare Price', 'Stock', 'In Stock', 'Rating', 'Reviews Count']);

        foreach ($products as $p) {
            fputcsv($output, [
                $p['id'],
                $p['sku'],
                $p['name'],
                $p['category_name'] ?? '',
                $p['price'],
                $p['compare_price'] ?? '',
                $p['stock_quantity'],
                $p['is_in_stock'] ? 'Yes' : 'No',
                $p['average_rating'],
                $p['reviews_count'],
            ]);
        }

        fclose($output);
        exit;
    }
}
