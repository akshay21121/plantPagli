<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Setting;

class PageController extends Controller {
    public function about(): void {
        $this->render('pages/about', [
            'title' => 'About Us - PlantPuglu India',
            'meta_description' => 'Learn about PlantPuglu - our story, sustainable nursery practices, and mission to bring nature into every Indian home.'
        ]);
    }

    public function contact(): void {
        $settingModel = new Setting();
        $this->render('pages/contact', [
            'title' => 'Contact Us - PlantPuglu Customer Support',
            'meta_description' => 'Get in touch with PlantPuglu customer care via WhatsApp, phone, or email.',
            'phone' => $settingModel->get('store_phone', '+91 98765 43210'),
            'email' => $settingModel->get('store_email', 'care@plantpagli.com'),
            'address' => $settingModel->get('store_address', 'Bengaluru, India'),
        ]);
    }

    public function sendContactMessage(): void {
        $this->validateCSRF();

        $rules = [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'message' => 'required|min:10',
        ];

        $validator = $this->validate($rules);
        if ($validator->fails()) {
            $this->redirect('/contact', 'error', 'Please fill in all contact form fields correctly.');
            return;
        }

        $this->redirect('/contact', 'success', 'Thank you for reaching out! Our plant care horticulturists will reply within 24 hours.');
    }

    public function plantCare(): void {
        $this->render('pages/plant_care', [
            'title' => 'Plant Care & Troubleshooting Guide - PlantPuglu',
            'meta_description' => 'Comprehensive plant care tips for Indian climates - sunlight, watering, soil mixes, and seasonal protection.'
        ]);
    }

    public function shippingPolicy(): void {
        $this->render('pages/shipping_policy', [
            'title' => 'Shipping & Delivery Policy - PlantPuglu',
        ]);
    }

    public function refundPolicy(): void {
        $this->render('pages/refund_policy', [
            'title' => 'Replacement & Guarantee Policy - PlantPuglu',
        ]);
    }

    public function privacyPolicy(): void {
        $this->render('pages/privacy_policy', [
            'title' => 'Privacy & Security Policy - PlantPuglu',
        ]);
    }
}
