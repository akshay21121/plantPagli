<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\User;

class CustomerController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $userModel = new User();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $customers = $userModel->getAllCustomers($limit, $offset);
        $total = $userModel->countCustomers();
        $totalPages = (int) ceil($total / $limit);

        $this->render('admin/customers/index', [
            'title' => 'Registered Customers - PlantPuglu Admin',
            'customers' => $customers,
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ], 'layouts/admin');
    }
}
