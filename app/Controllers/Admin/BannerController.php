<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Banner;
use App\Services\UploadService;
use Exception;

class BannerController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $bannerModel = new Banner();
        $banners = $bannerModel->getAllForAdmin();

        $this->render('admin/banners/index', [
            'title' => 'Banners & Announcements - PlantPuglu Admin',
            'banners' => $banners,
        ], 'layouts/admin');
    }

    public function store(): void {
        $this->validateCSRF();

        $bannerModel = new Banner();
        $data = $_POST;

        if (!empty($_FILES['image']['name'])) {
            try {
                $filename = UploadService::uploadImage($_FILES['image'], 'banners');
                $data['image_path'] = $filename;
            } catch (Exception $e) {
                $this->redirect('/admin/banners', 'error', 'Image error: ' . $e->getMessage());
                return;
            }
        }

        $bannerModel->create($data);
        $this->redirect('/admin/banners', 'success', 'New banner/announcement saved.');
    }

    public function delete(string $id): void {
        $this->validateCSRF();
        $bannerModel = new Banner();
        $bannerModel->delete((int)$id);
        $this->redirect('/admin/banners', 'info', 'Banner removed.');
    }
}
