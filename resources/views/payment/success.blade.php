<x-app-layout>
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="bg-white rounded-2xl shadow-lg p-10 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">To'lov muvaffaqiyatli!</h1>
        <p class="text-gray-500 mb-8">Xizmat faollashtirildi. 30 kun davomida foydalanishingiz mumkin.</p>
        <a href="{{ route('products.index') }}"
           class="inline-block bg-green-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-green-700 transition">
            Marketplaysga o'tish
        </a>
    </div>
</div>
</x-app-layout>
