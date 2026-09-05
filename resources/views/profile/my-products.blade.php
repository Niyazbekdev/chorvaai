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

.stat-card { border-radius: 16px; padding: 18px 20px; }

.status-badge {
    display: inline-block; font-size: .68rem; font-weight: 700;
    padding: 3px 9px; border-radius: 5px; text-transform: uppercase; letter-spacing: .04em;
}
.status-faol    { background: #E2ECDF; color: #1D3520; }
.status-sotildi { background: #f0f0f0; color: #6b7280; }
.status-pending { background: #F6ECD7; color: #B5822A; }

.filter-tab {
    padding: 7px 16px; border-radius: 999px; font-size: .84rem; font-weight: 600;
    border: none; cursor: pointer; background: transparent; color: #5C6352;
    text-decoration: none; transition: background .15s, color .15s;
    white-space: nowrap;
}
.filter-tab.active { background: #1D3520; color: white; }
.filter-tab:hover:not(.active) { background: #EDF0E5; color: #1D3520; }

.product-row { display: flex; align-items: center; gap: 14px; padding: 14px 16px; border-bottom: 1px solid #EDF0E5; }
.product-row:last-child { border-bottom: none; }
.product-row:hover { background: #FAFCF9; }

.icon-btn {
    width: 34px; height: 34px; border-radius: 8px; border: 1.5px solid #E2ECDF;
    background: white; display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: #5C6352; transition: all .15s; flex-shrink: 0;
    text-decoration: none;
}
.icon-btn:hover { border-color: #1D3520; color: #1D3520; background: #F8FCF7; }
.icon-btn.danger:hover { border-color: #A34F30; color: #A34F30; background: #F5E3DB; }
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
                        @if($user->avatar)
                            <img src="{{ $user->avatar_url }}" alt=""
                                 class="w-14 h-14 rounded-full object-cover mb-3">
                        @else
                            <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg mb-3"
                                 style="background:#E2ECDF;color:#1D3520;">
                                {{ mb_strtoupper(mb_substr($user->first_name,0,1).mb_substr($user->last_name,0,1)) }}
                            </div>
                        @endif
                        <p class="font-bold text-sm text-ink leading-tight">{{ $user->first_name }} {{ $user->last_name }}</p>
                        <span class="mt-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full" style="background:#E2ECDF;color:#3E683F;">
                            {{ __('profile.verified') }}
                        </span>
                    </div>

                    {{-- Nav --}}
                    <nav class="space-y-1">
                        <a href="{{ route('profile.my-products') }}" class="profile-nav-link active">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            {{ __('profile.my_ads') }}
                        </a>
                        <a href="{{ route('profile.favorites') }}" class="profile-nav-link">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            {{ __('profile.favorites_tab') }}
                        </a>
                        <a href="{{ route('profile.edit') }}" class="profile-nav-link">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('profile.profile_title') }}
                        </a>
                    </nav>
                </div>
            </div>

            {{-- ═══ MAIN CONTENT ═══ --}}
            <div class="flex-1 min-w-0 space-y-5">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="font-serif text-2xl font-bold text-ink">{{ __('profile.my_ads') }}</h1>
                        <p class="text-sm mt-0.5" style="color:#5C6352;">{{ __('profile.stats_30days') }}</p>
                    </div>
                    <a href="{{ route('products.create') }}"
                       style="background:#1D3520;color:white;padding:10px 18px;border-radius:10px;font-weight:700;font-size:.875rem;text-decoration:none;display:flex;align-items:center;gap:7px;flex-shrink:0;"
                       onmouseover="this.style.background='#2C4E2E'" onmouseout="this.style.background='#1D3520'">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        {{ __('profile.new_ad_btn') }}
                    </a>
                </div>

                {{-- Stat cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- Faol e'lonlar --}}
                    <div class="stat-card bg-white" style="border:1px solid #EDF0E5;">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#5C6352;">{{ __('profile.active_ads') }}</p>
                        <p class="font-serif font-bold text-3xl text-ink">{{ $stats['active'] }}</p>
                        @if($stats['pending'] > 0)
                            <p class="text-xs mt-1" style="color:#5C6352;">{{ $stats['pending'] }} {{ __('profile.filter_pending') }}</p>
                        @endif
                    </div>
                    {{-- Umumiy ko'rishlar --}}
                    <div class="stat-card" style="background:#F6ECD7;border:1px solid #e8d5a8;">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#9a6d22;">{{ __('profile.total_views_stat') }}</p>
                        <p class="font-serif font-bold text-3xl" style="color:#B5822A;">{{ number_format($stats['total_views']) }}</p>
                        <p class="text-xs mt-1" style="color:#9a6d22;">{{ __('profile.total_ads_hint') }}</p>
                    </div>
                    {{-- Telefon ochilishi --}}
                    <div class="stat-card" style="background:#EFF6FF;border:1px solid #bfdbfe;">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#3b82f6;">{{ __('profile.phone_reveals') }}</p>
                        <p class="font-serif font-bold text-3xl" style="color:#2563eb;">
                            {{ $products->sum('phone_views_count') }}
                        </p>
                        <p class="text-xs mt-1" style="color:#3b82f6;">{{ __('profile.phone_reveals_hint') }}</p>
                    </div>
                    {{-- Sotilgan --}}
                    <div class="stat-card" style="background:#EDF0E5;border:1px solid #d4ddd0;">
                        <p class="text-xs font-semibold uppercase tracking-wide mb-2" style="color:#5C6352;">{{ __('profile.sold_stat') }}</p>
                        <p class="font-serif font-bold text-3xl text-ink">{{ $stats['sold'] }}</p>
                        @if($stats['total_value'] > 0)
                            <p class="text-xs mt-1" style="color:#5C6352;">{{ __('profile.total_value_hint', ['mln' => number_format($stats['total_value']/1000000, 0)]) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="px-4 py-3 rounded-xl text-sm" style="background:#E2ECDF;color:#1D3520;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="px-4 py-3 rounded-xl text-sm" style="background:#F5E3DB;color:#A34F30;">{{ session('error') }}</div>
                @endif

                {{-- Filter tabs --}}
                @php
                    $filter   = request('filter', 'all');
                    $allCount = $products->count();
                    $activeCount  = $products->filter(fn($p) => $p->status?->name === 'Faol')->count();
                    $pendingCount = $products->filter(fn($p) => $p->status?->name === "Ko'rib chiqilmoqda")->count();
                    $soldCount    = $products->filter(fn($p) => $p->status?->name === 'Sotildi')->count();

                    $displayed = match($filter) {
                        'active'  => $products->filter(fn($p) => $p->status?->name === 'Faol'),
                        'pending' => $products->filter(fn($p) => $p->status?->name === "Ko'rib chiqilmoqda"),
                        'sold'    => $products->filter(fn($p) => $p->status?->name === 'Sotildi'),
                        default   => $products,
                    };
                @endphp

                <div class="flex gap-1.5 flex-wrap">
                    <a href="{{ route('profile.my-products') }}" class="filter-tab {{ $filter === 'all' ? 'active' : '' }}">
                        {{ __('profile.filter_all') }} · {{ $allCount }}
                    </a>
                    <a href="{{ route('profile.my-products', ['filter' => 'active']) }}" class="filter-tab {{ $filter === 'active' ? 'active' : '' }}">
                        {{ __('profile.active') }} · {{ $activeCount }}
                    </a>
                    <a href="{{ route('profile.my-products', ['filter' => 'pending']) }}" class="filter-tab {{ $filter === 'pending' ? 'active' : '' }}">
                        {{ __('profile.filter_pending') }} · {{ $pendingCount }}
                    </a>
                    <a href="{{ route('profile.my-products', ['filter' => 'sold']) }}" class="filter-tab {{ $filter === 'sold' ? 'active' : '' }}">
                        {{ __('profile.filter_sold') }} · {{ $soldCount }}
                    </a>
                </div>

                {{-- Product list --}}
                @if($displayed->isEmpty())
                    <div class="bg-white rounded-2xl p-16 text-center" style="border:1px solid #EDF0E5;">
                        <p class="text-4xl mb-3">📭</p>
                        <p class="font-bold text-base text-ink mb-1">{{ __('profile.no_ads_now') }}</p>
                        <p class="text-sm mb-5" style="color:#5C6352;">{{ __('profile.no_ads_place') }}</p>
                        <a href="{{ route('products.create') }}"
                           style="display:inline-block;background:#1D3520;color:white;padding:11px 24px;border-radius:10px;font-weight:700;font-size:.875rem;text-decoration:none;">
                            {{ __('profile.post_ad_btn') }}
                        </a>
                    </div>
                @else
                    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #EDF0E5;">
                        @foreach($displayed as $product)
                            @php
                                $statusName = $product->status?->name ?? '';
                                $isSold     = $statusName === 'Sotildi';
                                $isPending  = $statusName === "Ko'rib chiqilmoqda";
                            @endphp
                            <div class="product-row">
                                {{-- Thumbnail --}}
                                <a href="{{ route('products.show', $product) }}"
                                   class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0"
                                   style="background:#EDF0E5;">
                                    @if($product->primary_image_url)
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-2xl">
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
                                </a>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="status-badge {{ $isSold ? 'status-sotildi' : ($isPending ? 'status-pending' : 'status-faol') }}">
                                            {{ strtoupper($statusName ?: 'FAOL') }}
                                        </span>
                                        @if($product->category)
                                            <span style="font-size:.68rem;font-weight:600;color:#5C6352;">{{ $product->category->name }}</span>
                                        @endif
                                    </div>
                                    <p class="font-bold text-sm text-ink leading-tight truncate">{{ $product->name }}</p>
                                    <p class="font-serif font-bold text-base" style="color:#1D3520;">{{ $product->formatted_price }}</p>
                                </div>

                                {{-- Stats --}}
                                <div class="hidden sm:flex items-center gap-4 text-xs flex-shrink-0" style="color:#5C6352;">
                                    <span class="flex items-center gap-1">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <strong class="text-ink">{{ number_format($product->views_count) }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        <strong class="text-ink">{{ $product->favorites_count }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <strong class="text-ink">{{ $product->phone_views_count }}</strong>
                                    </span>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if(!$isSold)
                                        <a href="{{ route('products.edit', $product) }}" class="icon-btn" title="{{ __('profile.edit') }}">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                          onsubmit="return confirm('{{ __('profile.delete_confirm_js') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-btn danger" title="{{ __('profile.delete') }}">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @if(!$isSold)
                                        <button onclick="openSoldModal({{ $product->id }}, {{ json_encode($product->name) }}, {{ $product->price }})"
                                                style="padding:7px 14px;border-radius:8px;font-weight:700;font-size:.78rem;border:none;cursor:pointer;background:#F6ECD7;color:#B5822A;white-space:nowrap;">
                                            {{ __('profile.sold_btn_label') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- Mark as Sold modal --}}
<div id="soldModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0" style="background:rgba(0,0,0,.5);" onclick="closeSoldModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm">
        <h3 class="font-serif text-xl font-bold text-ink mb-1">{{ __('profile.mark_sold_title') }}</h3>
        <p class="text-sm mb-5" style="color:#5C6352;" id="soldProductName"></p>
        <form method="POST" id="soldForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">{{ __('profile.sold_price_label') }}</label>
                <input type="number" name="sold_price" id="soldPrice" min="0"
                       style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#3E683F'" onblur="this.style.borderColor='#E2ECDF'">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color:#5C6352;">{{ __('profile.sale_source_label') }}</label>
                <select name="source"
                        style="width:100%;border:1.5px solid #E2ECDF;border-radius:10px;padding:11px 14px;font-size:.9rem;outline:none;background:white;box-sizing:border-box;">
                    <option value="outside">{{ __('profile.outside_option') }}</option>
                    <option value="phone_call">{{ __('profile.phone_option') }}</option>
                    <option value="platform_chat">{{ __('profile.platform_option') }}</option>
                </select>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeSoldModal()"
                        style="flex:1;padding:12px;border:1.5px solid #E2ECDF;border-radius:10px;font-weight:600;background:white;cursor:pointer;color:#191D14;">
                    {{ __('profile.cancel') }}
                </button>
                <button type="submit"
                        style="flex:1;padding:12px;background:#1D3520;color:white;border-radius:10px;font-weight:700;border:none;cursor:pointer;">
                    {{ __('profile.confirm') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openSoldModal(id, name, price) {
    document.getElementById('soldProductName').textContent = name;
    document.getElementById('soldPrice').value = price;
    document.getElementById('soldForm').action = `/products/${id}/mark-sold`;
    document.getElementById('soldModal').classList.remove('hidden');
}
function closeSoldModal() {
    document.getElementById('soldModal').classList.add('hidden');
}
</script>
</x-app-layout>
