<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Session;
use App\Models\Order;

class OrderController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $orderModel = new Order();

        $status = $_GET['status'] ?? null;
        $paymentStatus = $_GET['payment_status'] ?? null;
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'status' => $status,
            'payment_status' => $paymentStatus,
            'search' => $search,
        ];

        $orders = $orderModel->getAllForAdmin($filters, $limit, $offset);
        $total = $orderModel->countForAdmin($filters);
        $totalPages = (int) ceil($total / $limit);

        $this->render('admin/orders/index', [
            'title' => 'Customer Orders - PlantPuglu Admin',
            'orders' => $orders,
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'status' => $status,
            'paymentStatus' => $paymentStatus,
            'search' => $search,
        ], 'layouts/admin');
    }

    public function show(string $id): void {
        $orderModel = new Order();
        $order = $orderModel->findById((int)$id);

        if (!$order) {
            $this->redirect('/admin/orders', 'error', 'Order not found.');
            return;
        }

        $this->render('admin/orders/show', [
            'title' => "Order #{$order['order_number']} - PlantPuglu Admin",
            'order' => $order,
        ], 'layouts/admin');
    }

    public function updateStatus(string $id): void {
        $this->validateCSRF();
        $orderId = (int) $id;

        $status = $_POST['order_status'] ?? 'pending';
        $trackingNumber = trim($_POST['tracking_number'] ?? '');
        $courierName = trim($_POST['courier_name'] ?? '');

        $orderModel = new Order();
        $orderModel->updateOrderStatus($orderId, $status, $trackingNumber ?: null, $courierName ?: null);

        $this->redirect("/admin/orders/{$orderId}", 'success', "Order status updated to '{$status}'.");
    }

    public function updatePaymentStatus(string $id): void {
        $this->validateCSRF();
        $orderId = (int) $id;
        $paymentStatus = $_POST['payment_status'] ?? 'pending';

        $orderModel = new Order();
        $orderModel->updatePaymentStatus($orderId, $paymentStatus);

        $this->redirect("/admin/orders/{$orderId}", 'success', "Payment status updated to '{$paymentStatus}'.");
    }

    public function exportCsv(): void {
        $orderModel = new Order();
        $orders = $orderModel->getAllForAdmin([], 5000, 0);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=plantpuglu_orders_' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Order Number', 'Date', 'Customer Name', 'Email', 'Phone',
            'City', 'State', 'PIN Code', 'Subtotal', 'Discount', 'Shipping', 'Total Amount',
            'Payment Method', 'Payment Status', 'Order Status', 'Tracking Number'
        ]);

        foreach ($orders as $o) {
            fputcsv($output, [
                $o['order_number'],
                $o['created_at'],
                $o['customer_name'],
                $o['customer_email'],
                $o['customer_phone'],
                $o['shipping_city'],
                $o['shipping_state'],
                $o['shipping_pincode'],
                $o['subtotal'],
                $o['discount_amount'],
                $o['shipping_fee'],
                $o['total_amount'],
                strtoupper($o['payment_method']),
                strtoupper($o['payment_status']),
                ucfirst($o['order_status']),
                $o['tracking_number'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }
}
