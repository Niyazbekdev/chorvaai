<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Create a pending payment and redirect to checkout page.
     * type: subscription | banner
     * provider: payme | click
     */
    public function initiate(Request $request): RedirectResponse
    {
        $request->validate([
            'type'      => 'required|in:subscription,banner',
            'provider'  => 'required|in:payme,click',
            'banner_id' => 'nullable|integer|exists:banners,id',
        ]);

        $meta = [];
        if ($request->filled('banner_id')) {
            $meta['banner_id'] = (int) $request->banner_id;
        }

        $payment = $this->paymentService->create(
            $request->user(),
            $request->type,
            $request->provider,
            $meta,
        );

        return redirect()->route('payment.checkout', $payment);
    }

    /**
     * Show checkout page — redirect user to Payme/Click.
     */
    public function checkout(Payment $payment): RedirectResponse|View
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$payment->isPending()) {
            return redirect()->route('payment.success')->with('payment_id', $payment->id);
        }

        // Immediately redirect to provider
        $url = $payment->provider === Payment::PROVIDER_PAYME
            ? $this->paymentService->paymeCheckoutUrl($payment)
            : $this->paymentService->clickCheckoutUrl($payment);

        return redirect()->away($url);
    }

    /**
     * Provider selection page (shown before initiating payment).
     * Called with ?type=subscription or ?type=banner
     */
    public function select(Request $request): View
    {
        $request->validate([
            'type'      => 'required|in:subscription,banner',
            'banner_id' => 'nullable|integer|exists:banners,id',
        ]);

        $type     = $request->type;
        $amount   = config("payment.amounts.$type");
        $bannerId = $request->banner_id;

        return view('payment.select', compact('type', 'amount', 'bannerId'));
    }

    public function success(Request $request): View
    {
        return view('payment.success');
    }

    public function failed(): View
    {
        return view('payment.failed');
    }
}
