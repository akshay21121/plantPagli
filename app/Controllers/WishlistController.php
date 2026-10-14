<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\Wishlist;

class WishlistController extends Controller {
    public function __construct() {
        $this->requireAuth();
    }

    public function index(): void {
        $userId = Auth::id();
        $wishlistModel = new Wishlist();
        $items = $wishlistModel->getByUser($userId);

        $this->render('wishlist/index', [
            'title' => 'My Wishlist - PlantPuglu',
            'items' => $items,
        ]);
    }

    public function toggle(): void {
        $this->validateCSRF();
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->respond(['success' => false, 'message' => 'Invalid product.'], 400);
            return;
        }

        $wishlistModel = new Wishlist();
        $res = $wishlistModel->toggle(Auth::id(), $productId);

        $this->respond([
            'success' => true,
            'added' => $res['added'],
            'message' => $res['message']
        ]);
    }

    public function remove(): void {
        $this->validateCSRF();
        $productId = (int) ($_POST['product_id'] ?? 0);

        $wishlistModel = new Wishlist();
        $wishlistModel->remove(Auth::id(), $productId);

        $this->redirect('/wishlist', 'info', 'Item removed from wishlist.');
    }

    private function respond(array $data, int $status = 200): void {
        if ($this->isAjax()) {
            $this->json($data, $status);
        } else {
            $this->redirect('/wishlist', $data['success'] ? 'success' : 'error', $data['message']);
        }
    }
}
