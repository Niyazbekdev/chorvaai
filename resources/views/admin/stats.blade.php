@extends('admin.layout')
@section('title', "E'lonlar statistikasi")

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.tab-btn {
    padding: 8px 20px; border-radius: 10px; font-size: .85rem; font-weight: 700;
    cursor: pointer; border: 1.5px solid #e5e7eb; background: white; color: #6b7280;
    transition: all .15s;
}
.tab-btn.active { background: #10b981; color: white; border-color: #10b981; }
.tab-btn:not(.active):hover { border-color: #10b981; color: #10b981; }
.stat-card { background: white; border-radius: 16px; padding: 20px 24px; border: 1px solid #f3f4f6; }
</style>
@endpush

@section('content')
<div x-data="statsPage()" x-init="init()">

    {{-- Tab tugmalari --}}
    <div class="flex gap-2 mb-6 flex-wrap">
        <button class="tab-btn" :class="{ active: tab === 'weekly'  }" @click="setTab('weekly')">Haftalik</button>
        <button class="tab-btn" :class="{ active: tab === 'daily'   }" @click="setTab('daily')">Kunlik</button>
        <button class="tab-btn" :class="{ active: tab === 'monthly' }" @click="setTab('monthly')">Oylik</button>
        <button class="tab-btn" :class="{ active: tab === 'yearly'  }" @click="setTab('yearly')">Yillik</button>
    </div>

    {{-- ══ HAFTALIK ══════════════════════════════════════════════ --}}
    <div x-show="tab === 'weekly'" x-cloak>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">7 kunlik jami</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $weeklyTotal }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">Kunlik o'rtacha</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $weeklyTotal > 0 ? round($weeklyTotal / 7, 1) : 0 }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">Eng ko'p bir kunda</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $weeklyTotal > 0 ? max($weeklyData) : 0 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4">So'nggi 7 kun — yangi e'lonlar</h3>
            <canvas id="chart-weekly" height="100"></canvas>
        </div>
    </div>

    {{-- ══ KUNLIK ════════════════════════════════════════════════ --}}
    <div x-show="tab === 'daily'" x-cloak>
        <form method="GET" action="{{ route('admin.stats') }}" class="flex items-center gap-3 mb-6">
            <input type="hidden" name="tab" value="daily">
            <label class="text-sm font-semibold text-gray-600">Kun tanlang:</label>
            <input type="date" name="date" value="{{ $selectedDate }}" max="{{ today()->toDateString() }}"
                   onchange="this.form.submit()"
                   class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
        </form>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">{{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }} jami</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $dailyTotal }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">Eng faol soat</p>
                @php $maxHour = $dailyTotal > 0 ? array_search(max($dailyData), $dailyData) : null; @endphp
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $maxHour !== null ? sprintf('%02d:00', $maxHour) : '—' }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">Soatlik o'rtacha</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $dailyTotal > 0 ? round($dailyTotal / 24, 1) : 0 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4">{{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }} — soat bo'yicha e'lonlar</h3>
            <canvas id="chart-daily" height="100"></canvas>
        </div>
    </div>

    {{-- ══ OYLIK ═════════════════════════════════════════════════ --}}
    <div x-show="tab === 'monthly'" x-cloak>
        <form method="GET" action="{{ route('admin.stats') }}" class="flex items-center gap-3 mb-6">
            <input type="hidden" name="tab" value="monthly">
            <label class="text-sm font-semibold text-gray-600">Oy tanlang:</label>
            <input type="month" name="month_year"
                   value="{{ sprintf('%04d-%02d', $selYear, $selMonth) }}"
                   max="{{ now()->format('Y-m') }}"
                   onchange="this.form.submit()"
                   class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
        </form>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">{{ $monthNames[$selMonth - 1] }} {{ $selYear }} jami</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $monthlyTotal }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">Kunlik o'rtacha</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ count($monthlyData) > 0 ? round($monthlyTotal / count($monthlyData), 1) : 0 }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">Eng ko'p bir kunda</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $monthlyTotal > 0 ? max($monthlyData) : 0 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4">{{ $monthNames[$selMonth - 1] }} {{ $selYear }} — kunlik e'lonlar</h3>
            <canvas id="chart-monthly" height="100"></canvas>
        </div>
    </div>

    {{-- ══ YILLIK ════════════════════════════════════════════════ --}}
    <div x-show="tab === 'yearly'" x-cloak>
        <form method="GET" action="{{ route('admin.stats') }}" class="flex items-center gap-3 mb-6">
            <input type="hidden" name="tab" value="yearly">
            <label class="text-sm font-semibold text-gray-600">Yil tanlang:</label>
            <select name="yearly_year" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $selYearlyYear ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">{{ $selYearlyYear }} yil jami</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $yearlyTotal }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">Oylik o'rtacha</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $yearlyTotal > 0 ? round($yearlyTotal / 12, 1) : 0 }}</p>
            </div>
            <div class="stat-card">
                <p class="text-sm text-gray-400 font-medium">Eng faol oy</p>
                @php $maxMonth = $yearlyTotal > 0 ? array_search(max($yearlyData), $yearlyData) : null; @endphp
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $maxMonth !== null ? $monthNames[$maxMonth] : '—' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-bold text-gray-700 mb-4">{{ $selYearlyYear }} — oylik e'lonlar</h3>
            <canvas id="chart-yearly" height="100"></canvas>
        </div>
    </div>

</div>

@push('scripts')
<script>
const WEEKLY_LABELS  = @json($weeklyLabels);
const WEEKLY_DATA    = @json($weeklyData);
const DAILY_LABELS   = @json($dailyLabels);
const DAILY_DATA     = @json($dailyData);
const MONTHLY_LABELS = @json($monthlyLabels);
const MONTHLY_DATA   = @json($monthlyData);
const YEARLY_LABELS  = @json($monthNames);
const YEARLY_DATA    = @json($yearlyData);

const ACTIVE_TAB = '{{ $tab }}';

const chartDefaults = {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { stepSize: 1, precision: 0 },
            grid: { color: '#f3f4f6' }
        },
        x: { grid: { display: false } }
    }
};

function makeBar(id, labels, data, color = '#10b981') {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: color + '33',
                borderColor: color,
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: chartDefaults
    });
}

function makeLine(id, labels, data, color = '#10b981') {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data,
                borderColor: color,
                backgroundColor: color + '18',
                borderWidth: 2.5,
                pointBackgroundColor: color,
                pointRadius: 4,
                fill: true,
                tension: 0.35,
            }]
        },
        options: chartDefaults
    });
}

function statsPage() {
    return {
        tab: ACTIVE_TAB,
        setTab(t) {
            this.tab = t;
            history.replaceState(null, '', '?tab=' + t);
            this.$nextTick(() => this.drawChart(t));
        },
        init() {
            this.$nextTick(() => this.drawChart(this.tab));
        },
        drawChart(t) {
            if (t === 'weekly')  makeLine('chart-weekly',  WEEKLY_LABELS,  WEEKLY_DATA);
            if (t === 'daily')   makeBar ('chart-daily',   DAILY_LABELS,   DAILY_DATA,   '#3b82f6');
            if (t === 'monthly') makeBar ('chart-monthly', MONTHLY_LABELS, MONTHLY_DATA, '#f59e0b');
            if (t === 'yearly')  makeLine('chart-yearly',  YEARLY_LABELS,  YEARLY_DATA,  '#8b5cf6');
        }
    };
}
</script>
@endpush
@endsection
