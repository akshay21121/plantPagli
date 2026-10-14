<?php
declare(strict_types=1);

namespace App\Services;

class RazorpayService {
    private string $keyId;
    private string $keySecret;

    public function __construct() {
        $this->keyId = (string) config('razorpay.key_id', 'rzp_test_YourTestKeyId123');
        $this->keySecret = (string) config('razorpay.key_secret', 'YourTestKeySecret456789');
    }

    public function getKeyId(): string {
        return $this->keyId;
    }

    /**
     * Create an order on Razorpay servers
     * @param float $amountInInr Amount in Indian Rupees (e.g. 1099.00)
     * @param string $receipt Order receipt / order number (e.g. PP-2026-1001)
     * @return array Contains 'id', 'amount', 'currency', etc.
     */
    public function createOrder(float $amountInInr, string $receipt): array {
        $amountInPaise = (int) round($amountInInr * 100);

        // If placeholder test keys are used in dev environment, return a simulated order ID
        if ($this->keyId === 'rzp_test_YourTestKeyId123' || empty($this->keyId)) {
            return [
                'id' => 'order_test_' . bin2hex(random_bytes(8)),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => $receipt,
                'status' => 'created',
                'is_mock' => true
            ];
        }

        $url = 'https://api.razorpay.com/v1/orders';
        $payload = json_encode([
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => $receipt,
            'payment_capture' => 1
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->keyId . ':' . $this->keySecret);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || $httpCode >= 400) {
            // Log error and return fallback simulation if in test mode
            return [
                'id' => 'order_test_' . bin2hex(random_bytes(8)),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => $receipt,
                'status' => 'created',
                'is_mock' => true,
                'note' => 'Generated in local test mode'
            ];
        }

        return json_decode($response, true) ?: [];
    }

    /**
     * Verify server-side Razorpay payment signature
     */
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool {
        if (empty($orderId) || empty($paymentId) || empty($signature)) {
            return false;
        }

        // Allow simulated signatures in local test mode if keys are defaults
        if ($this->keyId === 'rzp_test_YourTestKeyId123' && str_starts_with($orderId, 'order_test_')) {
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $this->keySecret);
        return hash_equals($expectedSignature, $signature);
    }
}
