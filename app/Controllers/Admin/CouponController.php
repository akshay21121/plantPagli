<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Session;
use App\Models\Coupon;

class CouponController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $couponModel = new Coupon();
        $coupons = $couponModel->getAll();

        $this->render('admin/coupons/index', [
            'title' => 'Discount Coupons - PlantPuglu Admin',
            'coupons' => $coupons,
        ], 'layouts/admin');
    }

    public function create(): void {
        $this->render('admin/coupons/create', [
            'title' => 'Create New Coupon - PlantPuglu Admin',
        ], 'layouts/admin');
    }

    public function store(): void {
        $this->validateCSRF();

        $rules = [
            'code' => 'required|min:3|max:50|unique:coupons,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            Session::set('_admin_errors', $validator->errors());
            $this->redirect('/admin/coupons/create', 'error', 'Please correct the coupon form errors.');
            return;
        }

        $couponModel = new Coupon();
        $couponModel->create($_POST);

        $this->redirect('/admin/coupons', 'success', "Coupon '{$_POST['code']}' created!");
    }

    public function delete(string $id): void {
        $this->validateCSRF();
        $couponModel = new Coupon();
        $couponModel->delete((int)$id);
        $this->redirect('/admin/coupons', 'info', 'Coupon deleted.');
    }
}
