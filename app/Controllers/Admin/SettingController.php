<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Setting;

class SettingController extends Controller {
    public function __construct() {
        $this->requireAdmin();
    }

    public function index(): void {
        $settingModel = new Setting();
        $settingsGrouped = $settingModel->getAllGrouped();

        $this->render('admin/settings/index', [
            'title' => 'Store & Gateway Settings - PlantPuglu Admin',
            'settingsGrouped' => $settingsGrouped,
        ], 'layouts/admin');
    }

    public function update(): void {
        $this->validateCSRF();
        $settingModel = new Setting();

        $settings = $_POST['settings'] ?? [];
        $settingModel->updateBatch($settings);

        $this->redirect('/admin/settings', 'success', 'Store configuration settings updated successfully.');
    }
}
