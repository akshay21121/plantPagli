<?php
declare(strict_types=1);

namespace App\Services;

use Exception;

class UploadService {
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_SIZE_BYTES = 5242880; // 5MB

    public static function uploadImage(array $file, string $subfolder = 'products'): string {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid file upload parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file was uploaded.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("Uploaded file exceeds maximum permitted file size.");
            default:
                throw new Exception("An unknown upload error occurred.");
        }

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            throw new Exception("File size cannot exceed 5 Megabytes.");
        }

        // Validate Extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            throw new Exception("Invalid file format. Allowed formats: JPG, PNG, WEBP.");
        }

        // Validate MIME type
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
                throw new Exception("Uploaded file content does not match a valid image.");
            }
        }

        // Target directory
        $targetDir = dirname(__DIR__, 2) . '/public/uploads/' . trim($subfolder, '/') . '/';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        if (!is_writable($targetDir)) {
            @chmod($targetDir, 0775);
            if (!is_writable($targetDir)) {
                throw new Exception("Upload directory is not writable. On your server, run: sudo chown -R www-data:www-data " . dirname(__DIR__, 2) . "/public/uploads && sudo chmod -R 775 " . dirname(__DIR__, 2) . "/public/uploads");
            }
        }

        $newFileName = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = $targetDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to move uploaded file. Check disk space or folder permissions on public/uploads.");
        }

        return $newFileName;
    }
}
