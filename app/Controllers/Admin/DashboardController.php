<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $orderModel = new Order();
        $stats = $orderModel->getDashboardStats();

        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard - PlantPuglu',
            'stats' => $stats,
        ], 'layouts/admin');
    }
}
