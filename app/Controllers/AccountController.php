<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use App\Models\User;
use App\Models\Order;

class AccountController extends Controller {
    public function __construct() {
        $this->requireAuth();
    }

    public function index(): void {
        $user = Auth::user();
        $orderModel = new Order();
        $userModel = new User();

        $orders = $orderModel->getCustomerOrders((int)$user['id']);
        $addresses = $userModel->getAddresses((int)$user['id']);

        $this->render('account/profile', [
            'title' => 'My Account - PlantPuglu',
            'user' => $user,
            'orders' => $orders,
            'addresses' => $addresses,
        ]);
    }

    public function updateProfile(): void {
        $this->validateCSRF();
        $userId = Auth::id();

        $rules = [
            'name' => 'required|min:2|max:100',
            'phone' => 'required|phone',
        ];

        // If password is being changed
        if (!empty($_POST['new_password'])) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'min:6';
            $rules['confirm_password'] = 'match:new_password';
        }

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            Session::flash('error', $validator->first('name') ?? $validator->first('phone') ?? 'Please check the form fields.');
            $this->redirect('/account');
            return;
        }

        $userModel = new User();
        $currentUser = $userModel->findById($userId);

        if (!empty($_POST['new_password'])) {
            if (!password_verify($_POST['current_password'], $currentUser['password'])) {
                $this->redirect('/account', 'error', 'Current password entered is incorrect.');
                return;
            }
        }

        $updateData = [
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
        ];

        if (!empty($_POST['new_password'])) {
            $updateData['password'] = $_POST['new_password'];
        }

        $userModel->update($userId, $updateData);
        $this->redirect('/account', 'success', 'Profile details updated successfully!');
    }

    public function orders(): void {
        $user = Auth::user();
        $orderModel = new Order();
        $orders = $orderModel->getCustomerOrders((int)$user['id']);

        $this->render('account/orders', [
            'title' => 'My Orders - PlantPuglu',
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    public function orderDetail(string $orderNumber): void {
        $user = Auth::user();
        $orderModel = new Order();
        $order = $orderModel->findByOrderNumber($orderNumber);

        if (!$order || (int)$order['user_id'] !== (int)$user['id']) {
            $this->redirect('/account/orders', 'error', 'Order not found.');
            return;
        }

        $this->render('account/order_detail', [
            'title' => "Order #{$order['order_number']} - PlantPuglu",
            'order' => $order,
        ]);
    }

    public function addresses(): void {
        $user = Auth::user();
        $userModel = new User();
        $addresses = $userModel->getAddresses((int)$user['id']);

        $this->render('account/addresses', [
            'title' => 'Saved Addresses - PlantPuglu',
            'user' => $user,
            'addresses' => $addresses,
        ]);
    }

    public function addAddress(): void {
        $this->validateCSRF();
        $userId = Auth::id();

        $rules = [
            'full_name' => 'required|min:2|max:100',
            'phone' => 'required|phone',
            'address_line1' => 'required|min:5',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required|pincode',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            $this->redirect('/account/addresses', 'error', 'Please provide valid address details.');
            return;
        }

        $userModel = new User();
        $userModel->addAddress($userId, [
            'label' => $_POST['label'] ?? 'Home',
            'full_name' => $_POST['full_name'],
            'phone' => $_POST['phone'],
            'address_line1' => $_POST['address_line1'],
            'address_line2' => $_POST['address_line2'] ?? '',
            'landmark' => $_POST['landmark'] ?? '',
            'city' => $_POST['city'],
            'state' => $_POST['state'],
            'pincode' => $_POST['pincode'],
            'is_default' => !empty($_POST['is_default']) ? 1 : 0,
        ]);

        $this->redirect('/account/addresses', 'success', 'New delivery address saved!');
    }

    public function deleteAddress(string $id): void {
        $this->validateCSRF();
        $userModel = new User();
        $userModel->deleteAddress((int)$id, Auth::id());
        $this->redirect('/account/addresses', 'info', 'Address removed.');
    }
}
