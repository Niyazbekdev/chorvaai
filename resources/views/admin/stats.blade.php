@extends('admin.layout')
@section('title', "E'lonlar statistikasi")

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.tab-btn { padding:9px 22px; border-radius:10px; font-size:.85rem; font-weight:700; cursor:pointer; border:1.5px solid #e5e7eb; background:white; color:#6b7280; transition:all .15s; }
.tab-btn.active { background:#10b981; color:white; border-color:#10b981; }
.tab-btn:not(.active):hover { border-color:#10b981; color:#10b981; }
.scard { background:white; border-radius:16px; padding:20px 24px; border:1px solid #f0fdf4; }
.scard-label { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#9ca3af; }
.scard-val { font-size:2rem; font-weight:800; line-height:1.1; margin-top:4px; }
.scard-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; margin-bottom:10px; }
[x-cloak] { display:none !important; }
</style>
@endpush

@section('content')
<div x-data="statsPage()" x-init="init()">

    {{-- Tabs --}}
    <div class="flex gap-2 mb-7 flex-wrap">
        <button class="tab-btn" :class="{ active: tab==='yearly'  }" @click="setTab('yearly')">Yillik</button>
        <button class="tab-btn" :class="{ active: tab==='monthly' }" @click="setTab('monthly')">Oylik</button>
        <button class="tab-btn" :class="{ active: tab==='custom'  }" @click="setTab('custom')">Belgilangan vaqt</button>
    </div>

    {{-- ══ YILLIK ══ --}}
    <div x-show="tab==='yearly'" x-cloak>
        <form method="GET" action="{{ route('admin.stats') }}" class="flex items-center gap-3 mb-6">
            <input type="hidden" name="tab" value="yearly">
            <label class="text-sm font-semibold text-gray-600">Yil:</label>
            <select name="year" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $selYear ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>

        <x-admin-stat-cards :data="$yearly" />

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-5">{{ $selYear }} — oylik ko'rsatkichlar</h3>
            <canvas id="chart-yearly" height="90"></canvas>
            <x-admin-chart-legend />
        </div>
    </div>

    {{-- ══ OYLIK ══ --}}
    <div x-show="tab==='monthly'" x-cloak>
        <form method="GET" action="{{ route('admin.stats') }}" class="flex items-center gap-3 mb-6">
            <input type="hidden" name="tab" value="monthly">
            <label class="text-sm font-semibold text-gray-600">Oy:</label>
            <input type="month" name="month_year"
                   value="{{ sprintf('%04d-%02d', $mYear, $mMonth) }}"
                   max="{{ now()->format('Y-m') }}"
                   onchange="this.form.submit()"
                   class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
        </form>

        <x-admin-stat-cards :data="$monthly" />

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-5">
                {{ ['Yan','Fev','Mar','Apr','May','Iyn','Iyl','Avg','Sen','Okt','Noy','Dek'][$mMonth-1] }}
                {{ $mYear }} — kunlik ko'rsatkichlar
            </h3>
            <canvas id="chart-monthly" height="90"></canvas>
            <x-admin-chart-legend />
        </div>
    </div>

    {{-- ══ BELGILANGAN VAQT ══ --}}
    <div x-show="tab==='custom'" x-cloak>
        <form method="GET" action="{{ route('admin.stats') }}" class="flex items-center gap-3 mb-6 flex-wrap">
            <input type="hidden" name="tab" value="custom">
            <label class="text-sm font-semibold text-gray-600">Dan:</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" max="{{ today()->toDateString() }}"
                   class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            <label class="text-sm font-semibold text-gray-600">Gacha:</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" max="{{ today()->toDateString() }}"
                   class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            <button type="submit"
                    class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-sm font-bold transition">
                Ko'rish
            </button>
        </form>

        <x-admin-stat-cards :data="$custom" />

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-5">
                {{ \Carbon\Carbon::parse($dateFrom)->format('d.m.Y') }} –
                {{ \Carbon\Carbon::parse($dateTo)->format('d.m.Y') }}
            </h3>
            <canvas id="chart-custom" height="90"></canvas>
            <x-admin-chart-legend />
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const DATA = {
    yearly:  { labels: @json($yearly['labels']),  products: @json($yearly['productData']),  sales: @json($yearly['saleData'])  },
    monthly: { labels: @json($monthly['labels']), products: @json($monthly['productData']), sales: @json($monthly['saleData']) },
    custom:  { labels: @json($custom['labels']),  products: @json($custom['productData']),  sales: @json($custom['saleData'])  },
};
const ACTIVE_TAB = '{{ $tab }}';
const charts = {};

function makeGrouped(id, d) {
    const el = document.getElementById(id);
    if (!el || charts[id]) return;
    charts[id] = new Chart(el, {
        type: 'bar',
        data: {
            labels: d.labels,
            datasets: [
                { label: "E'lonlar berildi", data: d.products, backgroundColor: '#10b98133', borderColor: '#10b981', borderWidth: 2, borderRadius: 5 },
                { label: 'Sotildi',           data: d.sales,    backgroundColor: '#f97316aa', borderColor: '#f97316', borderWidth: 2, borderRadius: 5 },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } },
            },
        },
    });
}

function statsPage() {
    return {
        tab: ACTIVE_TAB,
        setTab(t) {
            this.tab = t;
            history.replaceState(null, '', '?tab=' + t);
            this.$nextTick(() => makeGrouped('chart-' + t, DATA[t]));
        },
        init() {
            this.$nextTick(() => makeGrouped('chart-' + this.tab, DATA[this.tab]));
        },
    };
}
</script>
@endpush
