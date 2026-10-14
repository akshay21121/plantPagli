<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;

class HomeController extends Controller {
    public function index(): void {
        $bannerModel = new Banner();
        $categoryModel = new Category();
        $productModel = new Product();
        $settingModel = new Setting();

        $heroBanners = $bannerModel->getByType('hero');
        $announcement = $bannerModel->getAnnouncement();
        $categories = $categoryModel->getAll(true);
        $featuredCategories = $categoryModel->getFeatured();

        $bestsellers = $productModel->getBestsellers(8);
        $featuredProducts = $productModel->getFeatured(8);
        $airPurifying = $productModel->getByCategory('air-purifying-plants', 4);
        $combos = $productModel->getByCategory('plant-combos-and-gift-plants', 4);

        $this->render('home/index', [
            'title' => $settingModel->get('site_meta_title', 'PlantPuglu | Buy Plants, Pots & Garden Supplies Online India'),
            'meta_description' => $settingModel->get('site_meta_description', 'India’s favourite online plant nursery store.'),
            'heroBanners' => $heroBanners,
            'announcement' => $announcement,
            'categories' => $categories,
            'featuredCategories' => $featuredCategories,
            'bestsellers' => $bestsellers,
            'featuredProducts' => $featuredProducts,
            'airPurifying' => $airPurifying,
            'combos' => $combos,
        ]);
    }
}
