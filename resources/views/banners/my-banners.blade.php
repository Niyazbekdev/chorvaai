<x-app-layout>
<div class="min-h-screen bg-gray-50 pt-8 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('banners.my_banners_title') }}</h1>
            <a href="{{ route('banners.create') }}"
               class="bg-green-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-green-700 transition">
                {{ __('banners.new_banner') }}
            </a>
        </div>

        @if($banners->isEmpty())
            <div class="bg-white rounded-2xl shadow p-12 text-center text-gray-400">
                <p class="text-4xl mb-3">📢</p>
                <p class="text-lg font-semibold text-gray-600">{{ __('banners.no_banners') }}</p>
                <p class="text-sm mt-1 mb-6">{{ __('banners.no_banners_hint') }}</p>
                <a href="{{ route('banners.create') }}"
                   class="inline-block bg-green-600 text-white px-7 py-3 rounded-xl font-semibold hover:bg-green-700 transition">
                    {{ __('banners.post_banner') }}
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($banners as $banner)
                <div class="bg-white rounded-2xl shadow overflow-hidden">
                    <div class="flex flex-col sm:flex-row">
                        {{-- Rasm --}}
                        <div class="sm:w-56 shrink-0">
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                 class="w-full h-32 sm:h-full object-cover">
                        </div>
                        {{-- Ma'lumot --}}
                        <div class="flex-1 p-5 flex flex-col justify-between gap-3">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $banner->title }}</h3>
                                    @php
                                        $badge = match($banner->status) {
                                            'active'          => [__('banners.status_active'), 'bg-green-100 text-green-700'],
                                            'archived'        => [__('banners.status_archived'), 'bg-gray-100 text-gray-500'],
                                            'pending_payment' => [__('banners.status_pending'), 'bg-yellow-100 text-yellow-700'],
                                            default           => [$banner->status, 'bg-gray-100 text-gray-500'],
                                        };
                                    @endphp
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0 {{ $badge[1] }}">
                                        {{ $badge[0] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $banner->contact }}</p>

                                @if($banner->starts_at && $banner->expires_at)
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $banner->starts_at->format('d.m.Y') }} —
                                        {{ $banner->expires_at->format('d.m.Y') }}
                                        @if($banner->status === 'active')
                                            · <span class="text-green-600 font-medium">
                                                {{ now()->diffInDays($banner->expires_at) }} {{ __('banners.days_left') }}
                                            </span>
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @if($banner->status === 'archived')
                                    <form method="POST" action="{{ route('banners.reactivate', $banner) }}">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs border border-green-500 text-green-600 px-4 py-1.5 rounded-xl hover:bg-green-50 font-semibold transition">
                                            {{ __('banners.reactivate') }}
                                        </button>
                                    </form>
                                @elseif($banner->status === 'pending_payment')
                                    <a href="{{ route('payment.select', ['type' => 'banner', 'banner_id' => $banner->id]) }}"
                                       class="text-xs bg-blue-600 text-white px-4 py-1.5 rounded-xl hover:bg-blue-700 font-semibold transition">
                                        {{ __('banners.make_payment') }}
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('banners.destroy', $banner) }}"
                                      onsubmit="return confirm('{{ __('banners.delete_confirm') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs border border-red-300 text-red-500 px-4 py-1.5 rounded-xl hover:bg-red-50 font-semibold transition">
                                        {{ __('banners.delete_btn') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
</x-app-layout>
