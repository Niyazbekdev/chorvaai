<x-app-layout>

{{-- ===== HERO ===== --}}
<section class="site-hero-fullscreen relative overflow-hidden" style="background:#1D3520; min-height:100dvh;">

    {{-- Decorative animal silhouette --}}
    <div class="absolute right-0 bottom-0 top-0 flex items-end justify-end pointer-events-none" style="width:50%;">
        <svg viewBox="0 0 600 500" class="w-full h-full opacity-10" fill="#ffffff" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMaxYMax meet">
            <ellipse cx="300" cy="320" rx="200" ry="140" fill="#ffffff" opacity=".12"/>
            <ellipse cx="300" cy="280" rx="160" ry="120" fill="#ffffff" opacity=".15"/>
            <circle cx="200" cy="200" r="70" fill="#ffffff" opacity=".15"/>
            <ellipse cx="300" cy="320" rx="200" ry="130" fill="#ffffff" opacity=".1"/>
            <rect x="180" y="370" width="30" height="90" rx="10" fill="#ffffff" opacity=".15"/>
            <rect x="230" y="380" width="30" height="80" rx="10" fill="#ffffff" opacity=".15"/>
            <rect x="340" y="370" width="30" height="90" rx="10" fill="#ffffff" opacity=".15"/>
            <rect x="390" y="380" width="30" height="80" rx="10" fill="#ffffff" opacity=".15"/>
            <ellipse cx="200" cy="195" rx="50" ry="65" fill="#ffffff" opacity=".18"/>
            <circle cx="175" cy="165" r="12" fill="#ffffff" opacity=".25"/>
            <path d="M148 155 Q138 140 145 130" stroke="#ffffff" stroke-width="8" stroke-linecap="round" fill="none" opacity=".25"/>
            <path d="M175 155 Q165 138 172 128" stroke="#ffffff" stroke-width="8" stroke-linecap="round" fill="none" opacity=".25"/>
        </svg>
    </div>

    <div class="relative z-10 h-full flex flex-col justify-center px-5 sm:px-[5%] max-w-[800px] pt-24 pb-36">
        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 mb-6">
            <span style="background:#2C4E2E;color:#E2ECDF;font-size:.78rem;font-weight:700;padding:5px 14px;border-radius:999px;letter-spacing:.05em;text-transform:uppercase;">
                O'zbekistonning birinchi chorva bozori
            </span>
        </div>

        <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight text-white mb-6 opacity-0 anim-1">
            Chorva mollari sotib olish<br>va sotish ekosistemi.
        </h1>

        <p class="text-white/60 text-base sm:text-lg leading-relaxed max-w-lg mb-10 opacity-0 anim-3">
            Biz chorva egalari uchun ularni oson sotib olish va sotish, hamda ertagni kunda kuzatuv yechimlaridan foydalanish imkonini beradigan yagona ekotizimni quryamiz.
        </p>

        <div class="flex flex-wrap gap-3 opacity-0 anim-4">
            @auth
                <a href="{{ route('profile.my-products') }}"
                   style="background:#B5822A;color:white;padding:13px 28px;border-radius:10px;font-weight:700;font-size:.95rem;text-decoration:none;transition:background .2s;"
                   onmouseover="this.style.background='#9a6d22'" onmouseout="this.style.background='#B5822A'">
                    Sotishni boshlash
                </a>
            @else
                <a href="{{ route('login') }}"
                   style="background:#B5822A;color:white;padding:13px 28px;border-radius:10px;font-weight:700;font-size:.95rem;text-decoration:none;transition:background .2s;"
                   onmouseover="this.style.background='#9a6d22'" onmouseout="this.style.background='#B5822A'">
                    Sotishni boshlash
                </a>
            @endauth
            <a href="{{ url('/marketplace') }}"
               style="background:transparent;color:white;padding:13px 28px;border-radius:10px;font-weight:700;font-size:.95rem;text-decoration:none;border:2px solid rgba(255,255,255,.3);transition:border-color .2s,background .2s;"
               onmouseover="this.style.borderColor='rgba(255,255,255,.6)'" onmouseout="this.style.borderColor='rgba(255,255,255,.3)'">
                Mahsulotlarni ko'rish
            </a>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="absolute bottom-0 left-0 right-0 z-10" style="background:rgba(0,0,0,.25);backdrop-filter:blur(10px);border-top:1px solid rgba(255,255,255,.08);">
        <div class="max-w-7xl mx-auto px-5 sm:px-10 py-5 flex items-center gap-10 sm:gap-20 flex-wrap">
            <div>
                <p class="text-white font-bold text-2xl sm:text-3xl font-serif">{{ number_format($stats['products']) }}</p>
                <p class="text-white/50 text-sm mt-0.5">Faol e'lon</p>
            </div>
            <div>
                <p class="text-white font-bold text-2xl sm:text-3xl font-serif">{{ number_format($stats['users']) }}</p>
                <p class="text-white/50 text-sm mt-0.5">Tasdiqlangan fermer</p>
            </div>
            <div>
                <p class="text-white font-bold text-2xl sm:text-3xl font-serif">{{ $stats['regions'] }}</p>
                <p class="text-white/50 text-sm mt-0.5">Viloyat</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== KATEGORIYALAR ===== --}}
<section class="py-16 bg-paper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-ink">Kategoriyalar</h2>
            <a href="{{ url('/marketplace') }}" style="color:#3E683F;font-weight:600;font-size:.9rem;text-decoration:none;display:flex;align-items:center;gap:4px;">
                Barchasi
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $catIcons = [
                    'Qoramol' => ['emoji' => '🐄', 'color' => '#E2ECDF', 'text' => '#1D3520'],
                    "Qo'y va echki" => ['emoji' => '🐑', 'color' => '#F6ECD7', 'text' => '#B5822A'],
                    'Ot va tuya' => ['emoji' => '🐴', 'color' => '#EDF0E5', 'text' => '#3E683F'],
                    'Parranda' => ['emoji' => '🐓', 'color' => '#F5E3DB', 'text' => '#A34F30'],
                ];
            @endphp
            @foreach($categories->take(4) as $cat)
                @php $ci = $catIcons[$cat->name] ?? ['emoji' => '🐾', 'color' => '#E2ECDF', 'text' => '#1D3520']; @endphp
                <a href="{{ route('products.index', ['category' => $cat->id]) }}"
                   class="rounded-2xl p-5 flex flex-col gap-3 hover:shadow-lg transition-shadow cursor-pointer text-decoration-none"
                   style="background:{{ $ci['color'] }};text-decoration:none;">
                    <div class="text-4xl">{{ $ci['emoji'] }}</div>
                    <div>
                        <p class="font-bold text-base" style="color:{{ $ci['text'] }}">{{ $cat->name }}</p>
                        <p class="text-xs mt-0.5" style="color:{{ $ci['text'] }};opacity:.6">
                            @if($cat->children->count() > 0)
                                {{ $cat->children->pluck('name')->take(3)->implode(' · ') }}
                            @endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== NIMA UCHUN ===== --}}
<section id="why" class="py-16 bg-ground">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-ink mb-3">Nima uchun bozorimizdan<br>foydalanasiz?</h2>
            <p class="text-ink-2 max-w-md text-sm leading-relaxed">Chorva mollarini xavfsizroq, tezroq va aniqroq savdo qilish tajribasini his eting.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $features = [
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>', 'title' => 'Tasdiqlangan ishonch', 'desc' => 'Har bir fermer tekshiriladi. Haqiqiy odamlar va haqiqiy chorva mollari.'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/>', 'title' => 'Ai yordamchi', 'desc' => "Sigir yoshini yuborating — AI zoti, vazni, yoshi va bozor bahosini aniqlaydi."],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>', 'title' => 'Xavfsiz yetkazib berish', 'desc' => "Chorva mollaringizni kerakli joyga ishonchli tarzda yetkazib beramiz."],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'title' => 'Adolatli narxlar', 'desc' => "Yashirin vositachi yo'q, to'lovsiz shaffof bozor narxlari."],
                ];
            @endphp
            @foreach($features as $f)
                <div class="bg-paper rounded-2xl p-6">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-4" style="background:#E2ECDF;">
                        <svg width="22" height="22" fill="none" stroke="#1D3520" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
                    </div>
                    <h3 class="font-bold text-base text-ink mb-2">{{ $f['title'] }}</h3>
                    <p class="text-ink-2 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== AI SECTION ===== --}}
<section class="py-16 bg-paper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl overflow-hidden" style="background:#EDF0E5;">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                {{-- Left --}}
                <div class="p-8 sm:p-12 flex flex-col justify-center">
                    <span style="background:#E2ECDF;color:#3E683F;font-size:.78rem;font-weight:700;padding:5px 12px;border-radius:999px;display:inline-flex;align-items:center;gap:6px;width:fit-content;margin-bottom:16px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        AI YORDAMCHI
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-ink mb-4">Rasm yuboring — narxni bilib oling</h2>
                    <p class="text-ink-2 text-sm leading-relaxed mb-8">Molning rasmini yuklang. AI zoti, taxminiy yoshi va vazn holati (BCS) baholaydi va saytdagi bozor narxini aytadi.</p>
                    <a href="{{ route('ai.index') }}"
                       style="background:#1D3520;color:white;padding:12px 24px;border-radius:10px;font-weight:700;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:8px;width:fit-content;"
                       onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Rasmni sinab ko'rish
                    </a>
                </div>
                {{-- Right — sample card --}}
                <div class="p-8 sm:p-12 flex items-center justify-end">
                    <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-sm">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center text-3xl" style="background:#EDF0E5;">🐄</div>
                            <div class="flex-1">
                                <table class="w-full text-xs text-ink-2">
                                    <tr><td class="py-0.5 font-semibold uppercase tracking-wide text-[10px]">ZOTI</td><td class="text-right font-bold text-ink">Golshteyn</td></tr>
                                    <tr><td class="py-0.5 font-semibold uppercase tracking-wide text-[10px]">TAXMINIY YOSH</td><td class="text-right font-bold text-ink">3–4 yosh</td></tr>
                                    <tr><td class="py-0.5 font-semibold uppercase tracking-wide text-[10px]">TAXMINIY VAZN</td><td class="text-right font-bold text-ink">490–540 kg</td></tr>
                                    <tr><td class="py-0.5 font-semibold uppercase tracking-wide text-[10px]">BADAN HOLATI</td><td class="text-right font-bold text-ink">BCS 6/9</td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-4" style="border-top:1px solid #EDF0E5;">
                            <span class="text-xs font-semibold uppercase tracking-wide text-ink-2">BOZOR BAHOSI</span>
                            <span class="font-serif font-bold text-lg" style="color:#1D3520;">17–20 mln so'm</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== QANDAY ISHLAYDI ===== --}}
<section class="py-16 bg-ground">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-ink mb-12">Qanday ishlaydi</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $steps = [
                    ['n' => '1', 'title' => 'Hisob yarating', 'desc' => "Fermer yoki xaridor sifatida necha daqiqada ro'yxatdan o'ting."],
                    ['n' => '2', 'title' => "Joylashing yoki ko'ring", 'desc' => "Fermerlar chorva mollarini joylaydi, xaridorlar tanishadi."],
                    ['n' => '3', 'title' => "Bog'laning", 'desc' => "To'g'ridan-to'g'ri musoqot qiling va narxni keltiring."],
                    ['n' => '4', 'title' => 'Bitim', 'desc' => "Xavfsiz to'lov va yetkazib berish tartiboti."],
                ];
            @endphp
            @foreach($steps as $s)
                <div class="flex flex-col gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-serif font-bold text-lg text-white"
                         style="background:#B5822A;">{{ $s['n'] }}</div>
                    <div>
                        <h3 class="font-bold text-base text-ink mb-1">{{ $s['title'] }}</h3>
                        <p class="text-ink-2 text-sm leading-relaxed">{{ $s['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== SO'NGGI E'LONLAR ===== --}}
<section class="py-16 bg-paper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-ink">So'nggi e'lonlar</h2>
            <a href="{{ url('/marketplace') }}"
               style="color:#3E683F;font-weight:600;font-size:.9rem;text-decoration:none;display:flex;align-items:center;gap:4px;">
                Bozorga o'tish
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($recentProducts as $product)
                <a href="{{ route('products.show', $product) }}" class="bg-white rounded-2xl overflow-hidden hover:shadow-lg transition-shadow" style="text-decoration:none;">
                    {{-- Image --}}
                    <div class="relative" style="height:180px;background:#EDF0E5;overflow:hidden;">
                        @if($product->primary_image_url)
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">
                                {{ match(true) {
                                    in_array($product->category?->name, ['Sigir','Buqa','Buzoq']) || $product->category?->parent?->name === 'Qoramol' => '🐄',
                                    in_array($product->category?->name, ["Qo'y"]) => '🐑',
                                    $product->category?->name === 'Echki' => '🐐',
                                    $product->category?->name === 'Ot' => '🐴',
                                    $product->category?->name === 'Tuya' => '🐪',
                                    default => '🐾'
                                } }}
                            </div>
                        @endif
                        {{-- Tags --}}
                        <div class="absolute top-2.5 left-2.5 flex gap-1.5">
                            @if($product->created_at->diffInDays() < 3)
                                <span style="background:#B5822A;color:white;font-size:.65rem;font-weight:700;padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:.04em;">Yangi</span>
                            @endif
                        </div>
                        {{-- Fav --}}
                        @auth
                        <div x-data="{ favorited: false }" @click.stop
                             class="absolute top-2.5 right-2.5">
                            <button @click="
                                fetch('{{ route('products.favorite', $product) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                }).then(r => r.json()).then(d => { favorited = d.favorited; })
                            " class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow">
                                <svg width="15" height="15" fill="none" :stroke="favorited ? '#B5822A' : '#5C6352'" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                          :fill="favorited ? '#B5822A' : 'none'"/>
                                </svg>
                            </button>
                        </div>
                        @else
                        <a href="{{ route('login') }}" @click.stop
                           class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-white flex items-center justify-center shadow">
                            <svg width="15" height="15" fill="none" stroke="#5C6352" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </a>
                        @endauth
                    </div>
                    {{-- Info --}}
                    <div class="p-4">
                        <div class="flex gap-1.5 mb-2 flex-wrap">
                            @if($product->category)
                                <span style="background:#EDF0E5;color:#3E683F;font-size:.72rem;font-weight:600;padding:3px 8px;border-radius:6px;">
                                    {{ $product->category?->parent?->name ?? $product->category?->name }}
                                </span>
                                @if($product->category->parent)
                                    <span style="background:#EDF0E5;color:#5C6352;font-size:.72rem;font-weight:600;padding:3px 8px;border-radius:6px;">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                            @endif
                            @if($product->gender)
                                <span style="background:#EDF0E5;color:#5C6352;font-size:.72rem;font-weight:600;padding:3px 8px;border-radius:6px;">
                                    {{ $product->gender === 'erkak' ? "Erkak" : "Urg'ochi" }}
                                </span>
                            @endif
                        </div>
                        <h3 class="font-bold text-ink text-sm leading-tight line-clamp-1 mb-1">{{ $product->name }}</h3>
                        <p class="font-serif font-bold text-lg mb-2" style="color:#1D3520;">{{ $product->formatted_price }}</p>
                        <div class="text-xs flex gap-3" style="color:#5C6352;">
                            @if($product->age)
                                <span>{{ $product->age }} yosh</span>
                            @endif
                            @if($product->weight)
                                <span>{{ $product->weight }} kg</span>
                            @endif
                        </div>
                        @if($product->city || $product->region)
                            <p class="text-xs mt-1.5 flex items-center gap-1" style="color:#5C6352;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') }}
                            </p>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-4 text-center py-16 text-ink-2">
                    <p class="text-4xl mb-3">🐄</p>
                    <p>Hozircha e'lonlar yo'q</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ===== CTA ===== --}}
<section id="contact" class="py-20" style="background:#F6ECD7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center justify-between gap-8">
        <div>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-ink mb-3">
                Chorva biznesingizni<br>rivojlantirishga tayyormisiz?
            </h2>
            <p class="text-ink-2">Minglab fermerlar va xaridorlar bilan O'zbekistonning yirik chorva bozoriga qo'shiling.</p>
        </div>
        <div class="flex gap-3 flex-wrap flex-shrink-0">
            @auth
                <a href="{{ route('products.create') }}"
                   style="background:#1D3520;color:white;padding:14px 28px;border-radius:10px;font-weight:700;font-size:.95rem;text-decoration:none;"
                   onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                    Bugun boshlash
                </a>
            @else
                <a href="{{ url('/register') }}"
                   style="background:#1D3520;color:white;padding:14px 28px;border-radius:10px;font-weight:700;font-size:.95rem;text-decoration:none;"
                   onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                    Bugun boshlash
                </a>
            @endauth
            <a href="{{ url('/marketplace') }}"
               style="background:white;color:#1D3520;padding:14px 28px;border-radius:10px;font-weight:700;font-size:.95rem;text-decoration:none;border:2px solid #1D3520;"
               onmouseover="this.style.background='#F8FCF7'" onmouseout="this.style.background='white'">
                Bozorga o'tish
            </a>
        </div>
    </div>
</section>

</x-app-layout>
