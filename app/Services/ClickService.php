<?php

namespace App\Services;

use App\Models\Payment;

class ClickService
{
    const ACTION_PREPARE  = 0;
    const ACTION_COMPLETE = 1;

    const ERR_SUCCESS         =  0;
    const ERR_SIGN_FAILED     = -1;
    const ERR_ORDER_NOT_FOUND = -5;
    const ERR_ALREADY_PAID    = -4;
    const ERR_CANCELLED       = -9;
    const ERR_BAD_REQUEST     = -8;

    public function __construct(private PaymentService $paymentService) {}

    public function verifySign(array $data, int $action): bool
    {
        $secretKey  = config('payment.click.secret_key');
        $serviceId  = config('payment.click.service_id');

        $signString = implode('', [
            $data['click_trans_id'],
            $serviceId,
            $secretKey,
            $data['merchant_trans_id'],
            $data['amount'],
            $action,
            $data['sign_time'],
        ]);

        return hash_equals(md5($signString), $data['sign_string'] ?? '');
    }

    public function prepare(array $data): array
    {
        if (!$this->verifySign($data, self::ACTION_PREPARE)) {
            return $this->response($data, self::ERR_SIGN_FAILED, 'Sign check failed');
        }

        $payment = $this->findPayment($data);

        if (!$payment) {
            return $this->response($data, self::ERR_ORDER_NOT_FOUND, 'Order not found');
        }

        if ($payment->isPaid()) {
            return $this->response($data, self::ERR_ALREADY_PAID, 'Already paid');
        }

        if ($payment->isCancelled()) {
            return $this->response($data, self::ERR_CANCELLED, 'Cancelled');
        }

        if ((float) $data['amount'] !== (float) $payment->amount) {
            return $this->response($data, self::ERR_BAD_REQUEST, 'Amount mismatch');
        }

        $payment->update([
            'provider_transaction_id' => (string) $data['click_trans_id'],
        ]);

        return $this->response($data, self::ERR_SUCCESS, '');
    }

    public function complete(array $data): array
    {
        if (!$this->verifySign($data, self::ACTION_COMPLETE)) {
            return $this->response($data, self::ERR_SIGN_FAILED, 'Sign check failed');
        }

        $payment = $this->findPayment($data);

        if (!$payment) {
            return $this->response($data, self::ERR_ORDER_NOT_FOUND, 'Order not found');
        }

        if ($payment->isPaid()) {
            return $this->response($data, self::ERR_ALREADY_PAID, 'Already paid');
        }

        if ($payment->isCancelled()) {
            return $this->response($data, self::ERR_CANCELLED, 'Cancelled');
        }

        if ((int) ($data['error'] ?? 0) !== 0) {
            $this->paymentService->markCancelled($payment);
            return $this->response($data, self::ERR_CANCELLED, 'Cancelled by user');
        }

        if ((float) $data['amount'] !== (float) $payment->amount) {
            return $this->response($data, self::ERR_BAD_REQUEST, 'Amount mismatch');
        }

        $this->paymentService->markPaid($payment);

        return $this->response($data, self::ERR_SUCCESS, '');
    }

    private function findPayment(array $data): ?Payment
    {
        $orderId = $data['merchant_trans_id'] ?? null;
        return $orderId
            ? Payment::where('id', $orderId)->where('provider', Payment::PROVIDER_CLICK)->first()
            : null;
    }

    private function response(array $data, int $error, string $desc): array
    {
        return [
            'click_trans_id'    => $data['click_trans_id']    ?? null,
            'merchant_trans_id' => $data['merchant_trans_id'] ?? null,
            'merchant_confirm_id' => $data['merchant_trans_id'] ?? null,
            'error'             => $error,
            'error_note'        => $desc,
        ];
    }
}
