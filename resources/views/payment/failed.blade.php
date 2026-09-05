<x-app-layout>
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="bg-white rounded-2xl shadow-lg p-10 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('payment.failed_title') }}</h1>
        <p class="text-gray-500 mb-8">{{ __('payment.failed_desc') }}</p>
        <a href="{{ url()->previous() }}"
           class="inline-block bg-gray-800 text-white font-semibold px-8 py-3 rounded-xl hover:bg-gray-900 transition">
            {{ __('payment.retry') }}
        </a>
    </div>
</div>
</x-app-layout>
