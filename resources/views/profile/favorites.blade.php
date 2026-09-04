<x-app-layout>
@push('styles')
<style>
.profile-nav-link {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 10px;
    font-size: .9rem; font-weight: 600; color: #5C6352;
    text-decoration: none; transition: background .15s, color .15s;
}
.profile-nav-link:hover { background: #EDF0E5; color: #1D3520; }
.profile-nav-link.active { background: #E2ECDF; color: #1D3520; }
.profile-nav-link svg { flex-shrink: 0; }

.fav-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #EDF0E5;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: box-shadow .2s, transform .2s;
}
.fav-card:hover {
    box-shadow: 0 8px 32px rgba(29,53,32,.1);
    transform: translateY(-2px);
}
</style>
@endpush

<div class="min-h-screen pb-16" style="background:#F8FCF7;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="flex gap-6">

            {{-- ═══ LEFT SIDEBAR ═══ --}}
            <div class="hidden lg:block w-[220px] flex-shrink-0">
                <div class="bg-white rounded-2xl p-5 sticky top-[72px]" style="border:1px solid #EDF0E5;">

                    {{-- User info --}}
                    <div class="flex flex-col items-center text-center mb-6 pb-5" style="border-bottom:1px solid #EDF0E5;">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar_url }}" alt=""
                                 class="w-14 h-14 rounded-full object-cover mb-3">
                        @else
                            <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg mb-3"
                                 style="background:#E2ECDF;color:#1D3520;">
                                {{ mb_strtoupper(mb_substr(auth()->user()->first_name,0,1).mb_substr(auth()->user()->last_name,0,1)) }}
                            </div>
                        @endif
                        <p class="font-bold text-sm text-ink leading-tight">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                        <span class="mt-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full" style="background:#E2ECDF;color:#3E683F;">
                            Tasdiqlangan
                        </span>
                    </div>

                    {{-- Nav --}}
                    <nav class="space-y-1">
                        <a href="{{ route('profile.my-products') }}" class="profile-nav-link">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Mening e'lonlarim
                        </a>
                        <a href="{{ route('profile.favorites') }}" class="profile-nav-link active">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Sevimlilar
                        </a>
                        <a href="{{ route('profile.edit') }}" class="profile-nav-link">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil sozlamalari
                        </a>
                    </nav>
                </div>
            </div>

            {{-- ═══ MAIN CONTENT ═══ --}}
            <div class="flex-1 min-w-0 space-y-5">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="font-serif text-2xl font-bold text-ink">Sevimlilar</h1>
                        <p class="text-sm mt-0.5" style="color:#5C6352;">{{ $products->total() }} ta e'lon saqlangan</p>
                    </div>
                    <a href="{{ route('products.index') }}"
                       style="background:#F8FCF7;color:#1D3520;padding:10px 18px;border-radius:10px;font-weight:700;font-size:.875rem;text-decoration:none;display:flex;align-items:center;gap:7px;flex-shrink:0;border:1.5px solid #E2ECDF;"
                       onmouseover="this.style.background='#EDF0E5'" onmouseout="this.style.background='#F8FCF7'">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Bozorga o'tish
                    </a>
                </div>

                {{-- Empty state --}}
                @if($products->isEmpty())
                    <div class="bg-white rounded-2xl p-16 text-center" style="border:1px solid #EDF0E5;">
                        <p class="text-5xl mb-4">🤍</p>
                        <p class="font-bold text-lg text-ink mb-1">Saqlangan e'lonlar yo'q</p>
                        <p class="text-sm mb-6" style="color:#5C6352;">Yoqqan e'lonlarni sevimlilar ro'yxatiga qo'shing</p>
                        <a href="{{ route('products.index') }}"
                           style="display:inline-block;background:#1D3520;color:white;padding:12px 28px;border-radius:10px;font-weight:700;font-size:.9rem;text-decoration:none;">
                            Bozorga o'tish
                        </a>
                    </div>

                {{-- Favorites grid --}}
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($products as $product)
                            <div class="fav-card" x-data="{ favorited: true }" x-show="favorited" x-transition>

                                {{-- Image --}}
                                <div class="relative overflow-hidden" style="height:188px;background:#EDF0E5;">
                                    @if($product->primary_image_url)
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-5xl">
                                            {{ match(true) {
                                                in_array($product->category?->name, ['Sigir','Buqa','Buzoq']) => '🐄',
                                                in_array($product->category?->name, ["Qo'y"]) => '🐑',
                                                $product->category?->name === 'Echki' => '🐐',
                                                $product->category?->name === 'Ot' => '🐴',
                                                $product->category?->name === 'Tuya' => '🐪',
                                                default => '🐾'
                                            } }}
                                        </div>
                                    @endif

                                    {{-- Remove fav button --}}
                                    <button @click="async function() {
                                                const r = await fetch('{{ route('products.favorite', $product) }}', {
                                                    method: 'POST',
                                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                                });
                                                const d = await r.json();
                                                if (!d.favorited) favorited = false;
                                            }()"
                                            class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full flex items-center justify-center shadow-md transition"
                                            style="background:white;">
                                        <svg width="16" height="16" fill="#A34F30" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </button>

                                    {{-- Category badge --}}
                                    @if($product->category)
                                        <span class="absolute bottom-2.5 left-2.5 text-xs font-bold px-2.5 py-1 rounded-lg"
                                              style="background:rgba(29,53,32,.85);color:#E2ECDF;backdrop-filter:blur(4px);">
                                            {{ $product->category->name }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Card body --}}
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="font-bold text-sm text-ink leading-tight line-clamp-2 mb-1">{{ $product->name }}</h3>
                                    <p class="font-serif font-bold text-xl" style="color:#1D3520;">{{ $product->formatted_price }}</p>

                                    <div class="mt-2 flex items-center gap-1.5 text-xs" style="color:#5C6352;">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ collect([$product->city?->name, $product->region?->name])->filter()->implode(', ') ?: '—' }}
                                    </div>

                                    @if($product->pivot->created_at)
                                        <p class="text-xs mt-1" style="color:#5C6352;">
                                            {{ \Carbon\Carbon::parse($product->pivot->created_at)->diffForHumans() }} saqlangan
                                        </p>
                                    @endif

                                    <a href="{{ route('products.show', $product) }}"
                                       class="mt-4 block text-center py-2.5 rounded-xl text-sm font-bold transition"
                                       style="background:#E2ECDF;color:#1D3520;"
                                       onmouseover="this.style.background='#1D3520';this.style.color='white'"
                                       onmouseout="this.style.background='#E2ECDF';this.style.color='#1D3520'">
                                        Batafsil ko'rish
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($products->hasPages())
                        <div class="pt-2">
                            {{ $products->links() }}
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>
</div>
</x-app-layout>
