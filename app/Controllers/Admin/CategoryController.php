<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Session;
use App\Models\Category;

class CategoryController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll(false);

        $this->render('admin/categories/index', [
            'title' => 'Product Categories - PlantPuglu Admin',
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function create(): void {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll(false);

        $this->render('admin/categories/create', [
            'title' => 'Add Category - PlantPuglu Admin',
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function store(): void {
        $this->validateCSRF();

        $rules = [
            'name' => 'required|min:2|max:100',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            $this->redirect('/admin/categories/create', 'error', 'Please provide a valid category name.');
            return;
        }

        $categoryModel = new Category();
        $categoryModel->create($_POST);

        $this->redirect('/admin/categories', 'success', 'New category created.');
    }

    public function edit(string $id): void {
        $categoryModel = new Category();
        $category = $categoryModel->findById((int)$id);

        if (!$category) {
            $this->redirect('/admin/categories', 'error', 'Category not found.');
            return;
        }

        $categories = $categoryModel->getAll(false);

        $this->render('admin/categories/edit', [
            'title' => 'Edit Category - ' . $category['name'],
            'category' => $category,
            'categories' => $categories,
        ], 'layouts/admin');
    }

    public function update(string $id): void {
        $this->validateCSRF();
        $categoryId = (int) $id;

        $rules = [
            'name' => 'required|min:2|max:100',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            $this->redirect("/admin/categories/edit/{$categoryId}", 'error', 'Please provide a valid category name.');
            return;
        }

        $categoryModel = new Category();
        $categoryModel->update($categoryId, $_POST);

        $this->redirect('/admin/categories', 'success', 'Category updated successfully.');
    }

    public function delete(string $id): void {
        $this->validateCSRF();
        $categoryModel = new Category();
        $deleted = $categoryModel->delete((int)$id);

        if (!$deleted) {
            $this->redirect('/admin/categories', 'error', 'Cannot delete category: products are assigned to it.');
            return;
        }

        $this->redirect('/admin/categories', 'info', 'Category removed.');
    }
}
