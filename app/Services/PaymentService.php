<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function create(User $user, string $type, string $provider, array $meta = []): Payment
    {
        $amount = config("payment.amounts.$type");

        return Payment::create([
            'user_id'  => $user->id,
            'type'     => $type,
            'amount'   => $amount,
            'provider' => $provider,
            'status'   => Payment::STATUS_PENDING,
            'meta'     => $meta ?: null,
        ]);
    }

    public function markPaid(Payment $payment): void
    {
        if ($payment->isPaid()) {
            return;
        }

        $payment->update([
            'status'  => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        $this->onSuccess($payment);
    }

    public function markCancelled(Payment $payment, ?int $reason = null): void
    {
        $meta = $payment->meta ?? [];
        if ($reason !== null) {
            $meta['cancel_reason'] = $reason;
        }
        $payment->update([
            'status' => Payment::STATUS_CANCELLED,
            'meta'   => $meta,
        ]);
    }

    public function paymeCheckoutUrl(Payment $payment): string
    {
        $merchantId = config('payment.payme.merchant_id');
        $params = implode(';', [
            "m={$merchantId}",
            "ac.order_id={$payment->id}",
            "a={$payment->amountInTiyins()}",
            "l=uz",
        ]);

        $base = config('payment.payme.checkout_url');
        return $base . '/' . base64_encode($params);
    }

    public function clickCheckoutUrl(Payment $payment): string
    {
        $base = config('payment.click.checkout_url');

        return $base . '?' . http_build_query([
            'service_id'        => config('payment.click.service_id'),
            'merchant_id'       => config('payment.click.merchant_id'),
            'amount'            => $payment->amount,
            'transaction_param' => $payment->id,
            'return_url'        => route('payment.success'),
        ]);
    }

    // Called after payment is confirmed. Each module hooks its own logic here.
    private function onSuccess(Payment $payment): void
    {
        try {
            match ($payment->type) {
                Payment::TYPE_SUBSCRIPTION => app(SubscriptionService::class)->activate($payment),
                Payment::TYPE_BANNER       => app(BannerService::class)->activate($payment),
            };
        } catch (\Throwable $e) {
            Log::error("PaymentService::onSuccess failed for payment #{$payment->id}: " . $e->getMessage());
        }
    }
}
