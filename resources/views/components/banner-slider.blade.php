@php
    $banners = \App\Models\Banner::active()->latest('starts_at')->get();
@endphp

@if($banners->isNotEmpty())
<div class="w-full mb-6" x-data="{
    current: 0,
    total: {{ $banners->count() }},
    timer: null,
    init() {
        this.start();
    },
    start() {
        this.timer = setInterval(() => { this.next(); }, 5000);
    },
    pause() { clearInterval(this.timer); },
    next() { this.current = (this.current + 1) % this.total; },
    prev() { this.current = (this.current - 1 + this.total) % this.total; },
}" @mouseenter="pause()" @mouseleave="start()">

    <div class="relative overflow-hidden rounded-2xl shadow-md bg-gray-900" style="aspect-ratio: 3/1; max-height: 240px;">

        @foreach($banners as $i => $banner)
        <div x-show="current === {{ $i }}"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0">

            @if($banner->url)
                <a href="{{ $banner->url }}" target="_blank" rel="noopener" class="block w-full h-full">
            @else
                <div class="block w-full h-full">
            @endif

                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                     class="w-full h-full object-cover">

                {{-- Overlay gradient + info --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 right-0 px-5 py-4 pointer-events-none">
                    <p class="text-white font-bold text-base leading-snug drop-shadow">{{ $banner->title }}</p>
                    <p class="text-white/75 text-xs mt-0.5">{{ $banner->contact }}</p>
                </div>

            @if($banner->url)
                </a>
            @else
                </div>
            @endif
        </div>
        @endforeach

        @if($banners->count() > 1)
        {{-- Prev / Next --}}
        <button @click="prev()"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white rounded-full p-1.5 transition z-10">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button @click="next()"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white rounded-full p-1.5 transition z-10">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Dots --}}
        <div class="absolute bottom-3 right-4 flex gap-1.5 z-10">
            @foreach($banners as $i => $banner)
            <button @click="current = {{ $i }}"
                    :class="current === {{ $i }} ? 'bg-white w-4' : 'bg-white/50 w-2'"
                    class="h-2 rounded-full transition-all duration-300"></button>
            @endforeach
        </div>
        @endif

        {{-- "Reklama" label --}}
        <span class="absolute top-2 right-3 bg-black/50 text-white/70 text-[10px] font-medium px-2 py-0.5 rounded-full z-10">
            Reklama
        </span>
    </div>
</div>
@endif
