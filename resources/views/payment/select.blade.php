<x-app-layout>
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        @if(session('limit_reached'))
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 flex items-center gap-2">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                {{ __('payment.limit_reached') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">
                @if($type === 'subscription')
                    {{ __('payment.subscription_title') }}
                @else
                    {{ __('payment.banner_title') }}
                @endif
            </h1>
            <p class="text-gray-500 text-sm mb-6">
                @if($type === 'subscription')
                    {{ __('payment.subscription_desc') }}
                @else
                    {{ __('payment.banner_desc') }}
                @endif
            </p>

            {{-- Narx --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center justify-between">
                <span class="text-gray-700 font-medium">{{ __('payment.period_label') }}</span>
                <span class="text-2xl font-bold text-green-700">
                    {{ number_format($amount, 0, '.', ' ') }} so'm
                </span>
            </div>

            {{-- To'lov tizimini tanlash --}}
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">{{ __('payment.select_provider') }}</p>

            <div class="flex flex-col gap-3">
                {{-- Payme --}}
                <form method="POST" action="{{ route('payment.initiate') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="provider" value="payme">
                    @if($bannerId)
                        <input type="hidden" name="banner_id" value="{{ $bannerId }}">
                    @endif
                    <button type="submit"
                        class="w-full flex items-center justify-between bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-5 rounded-xl transition">
                        <span class="flex items-center gap-3">
                            <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                <rect width="40" height="40" rx="8" fill="white" fill-opacity=".2"/>
                                <text x="4" y="27" font-size="18" font-weight="900" fill="white">P</text>
                            </svg>
                            {{ __('payment.pay_payme') }}
                        </span>
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>

                {{-- Click --}}
                <form method="POST" action="{{ route('payment.initiate') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="provider" value="click">
                    @if($bannerId)
                        <input type="hidden" name="banner_id" value="{{ $bannerId }}">
                    @endif
                    <button type="submit"
                        class="w-full flex items-center justify-between bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-4 px-5 rounded-xl transition">
                        <span class="flex items-center gap-3">
                            <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
                                <rect width="40" height="40" rx="8" fill="white" fill-opacity=".2"/>
                                <text x="6" y="27" font-size="16" font-weight="900" fill="white">C</text>
                            </svg>
                            {{ __('payment.pay_click') }}
                        </span>
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
            </div>

            <p class="text-xs text-gray-400 text-center mt-5">
                {{ __('payment.secure_note') }}
            </p>
        </div>

        <div class="text-center mt-4">
            <a href="{{ url()->previous() }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('payment.back') }}</a>
        </div>

    </div>
</div>
</x-app-layout>
