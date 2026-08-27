<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymeService
{
    // Payme error codes
    const ERR_INSUFFICIENT_PRIVILEGE  = -32504;
    const ERR_INTERNAL_SYSTEM         = -32400;
    const ERR_ORDER_NOT_FOUND         = -31001;
    const ERR_AMOUNT_MISMATCH         = -31001;
    const ERR_TRANSACTION_NOT_FOUND   = -31003;
    const ERR_TRANSACTION_NOT_ALLOWED = -31008;
    const ERR_TRANSACTION_CANCELLED   = -31007;

    // Payme transaction states
    const STATE_CREATED   = 1;
    const STATE_PAID      = 2;
    const STATE_CANCELLED = -1;
    const STATE_REFUNDED  = -2;

    public function __construct(private PaymentService $paymentService) {}

    public function verifyAuth(Request $request): bool
    {
        $header = $request->header('Authorization', '');
        if (!str_starts_with($header, 'Basic ')) {
            return false;
        }

        $decoded  = base64_decode(substr($header, 6));
        $parts    = explode(':', $decoded, 2);
        $password = $parts[1] ?? '';

        $expectedKey = config('payment.payme.test_mode')
            ? config('payment.payme.test_key')
            : config('payment.payme.key');

        return hash_equals($expectedKey, $password);
    }

    public function handle(array $rpc): array
    {
        return match ($rpc['method'] ?? '') {
            'CheckPerformTransaction' => $this->checkPerform($rpc),
            'CreateTransaction'       => $this->createTransaction($rpc),
            'PerformTransaction'      => $this->performTransaction($rpc),
            'CancelTransaction'       => $this->cancelTransaction($rpc),
            'CheckTransaction'        => $this->checkTransaction($rpc),
            'GetStatement'            => $this->getStatement($rpc),
            default                   => $this->error($rpc['id'] ?? null, self::ERR_INTERNAL_SYSTEM, 'Unknown method'),
        };
    }

    // ── Methods ───────────────────────────────────────────────────────────────

    private function checkPerform(array $rpc): array
    {
        $params  = $rpc['params'];
        $payment = $this->findPayment($params);

        if (!$payment) {
            return $this->error($rpc['id'], self::ERR_ORDER_NOT_FOUND, 'Order not found');
        }

        if ($payment->amountInTiyins() !== (int) $params['amount']) {
            return $this->error($rpc['id'], self::ERR_AMOUNT_MISMATCH, 'Amount mismatch');
        }

        if (!$payment->isPending()) {
            return $this->error($rpc['id'], self::ERR_TRANSACTION_NOT_ALLOWED, 'Already processed');
        }

        return $this->result($rpc['id'], ['allow' => true]);
    }

    private function createTransaction(array $rpc): array
    {
        $params  = $rpc['params'];
        $payment = $this->findPayment($params);

        if (!$payment) {
            return $this->error($rpc['id'], self::ERR_ORDER_NOT_FOUND, 'Order not found');
        }

        if ($payment->amountInTiyins() !== (int) $params['amount']) {
            return $this->error($rpc['id'], self::ERR_AMOUNT_MISMATCH, 'Amount mismatch');
        }

        $paymeTxId   = $params['id'];
        $createTime  = (int) $params['time'];

        // If a transaction already exists for this Payme ID
        if ($payment->provider_transaction_id && $payment->provider_transaction_id !== $paymeTxId) {
            return $this->error($rpc['id'], self::ERR_TRANSACTION_NOT_ALLOWED, 'Transaction conflict');
        }

        if ($payment->isCancelled()) {
            return $this->error($rpc['id'], self::ERR_TRANSACTION_CANCELLED, 'Cancelled');
        }

        if (!$payment->provider_transaction_id) {
            $meta = $payment->meta ?? [];
            $meta['create_time'] = $createTime;
            $payment->update([
                'provider_transaction_id' => $paymeTxId,
                'meta'                    => $meta,
            ]);
        }

        return $this->result($rpc['id'], [
            'create_time' => $payment->meta['create_time'],
            'transaction' => (string) $payment->id,
            'state'       => self::STATE_CREATED,
        ]);
    }

    private function performTransaction(array $rpc): array
    {
        $paymeTxId = $rpc['params']['id'];
        $payment   = Payment::where('provider_transaction_id', $paymeTxId)->first();

        if (!$payment) {
            return $this->error($rpc['id'], self::ERR_TRANSACTION_NOT_FOUND, 'Transaction not found');
        }

        if ($payment->isCancelled()) {
            return $this->error($rpc['id'], self::ERR_TRANSACTION_CANCELLED, 'Transaction cancelled');
        }

        if ($payment->isPaid()) {
            return $this->result($rpc['id'], [
                'transaction'  => (string) $payment->id,
                'perform_time' => $payment->meta['perform_time'] ?? (now()->timestamp * 1000),
                'state'        => self::STATE_PAID,
            ]);
        }

        $performTime = now()->timestamp * 1000;
        $meta        = $payment->meta ?? [];
        $meta['perform_time'] = $performTime;
        $payment->update(['meta' => $meta]);

        $this->paymentService->markPaid($payment);

        return $this->result($rpc['id'], [
            'transaction'  => (string) $payment->id,
            'perform_time' => $performTime,
            'state'        => self::STATE_PAID,
        ]);
    }

    private function cancelTransaction(array $rpc): array
    {
        $paymeTxId = $rpc['params']['id'];
        $reason    = (int) ($rpc['params']['reason'] ?? 0);
        $payment   = Payment::where('provider_transaction_id', $paymeTxId)->first();

        if (!$payment) {
            return $this->error($rpc['id'], self::ERR_TRANSACTION_NOT_FOUND, 'Transaction not found');
        }

        if ($payment->isPaid()) {
            return $this->error($rpc['id'], self::ERR_TRANSACTION_NOT_ALLOWED, 'Already paid');
        }

        $cancelTime = now()->timestamp * 1000;
        $meta       = $payment->meta ?? [];
        $meta['cancel_time'] = $cancelTime;

        $payment->update(['meta' => $meta]);
        $this->paymentService->markCancelled($payment, $reason);

        return $this->result($rpc['id'], [
            'transaction' => (string) $payment->id,
            'cancel_time' => $cancelTime,
            'state'       => self::STATE_CANCELLED,
        ]);
    }

    private function checkTransaction(array $rpc): array
    {
        $paymeTxId = $rpc['params']['id'];
        $payment   = Payment::where('provider_transaction_id', $paymeTxId)->first();

        if (!$payment) {
            return $this->error($rpc['id'], self::ERR_TRANSACTION_NOT_FOUND, 'Transaction not found');
        }

        $state = match ($payment->status) {
            Payment::STATUS_PAID      => self::STATE_PAID,
            Payment::STATUS_CANCELLED => self::STATE_CANCELLED,
            default                   => self::STATE_CREATED,
        };

        $meta = $payment->meta ?? [];

        return $this->result($rpc['id'], [
            'create_time'  => $meta['create_time']  ?? 0,
            'perform_time' => $meta['perform_time'] ?? 0,
            'cancel_time'  => $meta['cancel_time']  ?? 0,
            'transaction'  => (string) $payment->id,
            'state'        => $state,
            'reason'       => $meta['cancel_reason'] ?? null,
        ]);
    }

    private function getStatement(array $rpc): array
    {
        $from = (int) $rpc['params']['from'];
        $to   = (int) $rpc['params']['to'];

        $payments = Payment::where('provider', Payment::PROVIDER_PAYME)
            ->whereNotNull('provider_transaction_id')
            ->whereBetween('created_at', [
                \Carbon\Carbon::createFromTimestampMs($from),
                \Carbon\Carbon::createFromTimestampMs($to),
            ])->get();

        $transactions = $payments->map(function (Payment $p) {
            $meta  = $p->meta ?? [];
            $state = match ($p->status) {
                Payment::STATUS_PAID      => self::STATE_PAID,
                Payment::STATUS_CANCELLED => self::STATE_CANCELLED,
                default                   => self::STATE_CREATED,
            };
            return [
                'id'           => $p->provider_transaction_id,
                'time'         => $meta['create_time'] ?? ($p->created_at->timestamp * 1000),
                'amount'       => $p->amountInTiyins(),
                'account'      => ['order_id' => $p->id],
                'create_time'  => $meta['create_time']  ?? 0,
                'perform_time' => $meta['perform_time'] ?? 0,
                'cancel_time'  => $meta['cancel_time']  ?? 0,
                'transaction'  => (string) $p->id,
                'state'        => $state,
                'reason'       => $meta['cancel_reason'] ?? null,
            ];
        });

        return $this->result($rpc['id'], ['transactions' => $transactions]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function findPayment(array $params): ?Payment
    {
        $orderId = $params['account']['order_id'] ?? null;
        if (!$orderId) {
            return null;
        }
        return Payment::where('id', $orderId)
            ->where('provider', Payment::PROVIDER_PAYME)
            ->first();
    }

    private function result(?int $id, array $result): array
    {
        return ['id' => $id, 'result' => $result];
    }

    private function error(?int $id, int $code, string $message): array
    {
        return [
            'id'    => $id,
            'error' => [
                'code'    => $code,
                'message' => [
                    'uz' => $message,
                    'ru' => $message,
                    'en' => $message,
                ],
                'data' => null,
            ],
        ];
    }
}
