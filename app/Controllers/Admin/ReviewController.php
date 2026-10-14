<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Review;

class ReviewController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $reviewModel = new Review();
        $reviews = $reviewModel->getAllForAdmin(50, 0);

        $this->render('admin/reviews/index', [
            'title' => 'Customer Reviews - PlantPuglu Admin',
            'reviews' => $reviews,
        ], 'layouts/admin');
    }

    public function approve(string $id): void {
        $this->validateCSRF();
        $reviewModel = new Review();
        $reviewModel->updateStatus((int)$id, 'approved');
        $this->redirect('/admin/reviews', 'success', 'Review approved and published.');
    }

    public function reject(string $id): void {
        $this->validateCSRF();
        $reviewModel = new Review();
        $reviewModel->updateStatus((int)$id, 'rejected');
        $this->redirect('/admin/reviews', 'info', 'Review marked as rejected.');
    }

    public function delete(string $id): void {
        $this->validateCSRF();
        $reviewModel = new Review();
        $reviewModel->delete((int)$id);
        $this->redirect('/admin/reviews', 'info', 'Review deleted.');
    }
}
