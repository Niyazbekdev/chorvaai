<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="scard">
        <div class="scard-icon bg-emerald-100">📋</div>
        <div class="scard-label">E'lonlar berildi</div>
        <div class="scard-val text-emerald-600">{{ number_format($data['totals']['products']) }}</div>
    </div>
    <div class="scard">
        <div class="scard-icon bg-orange-100">🤝</div>
        <div class="scard-label">Sotildi</div>
        <div class="scard-val text-orange-500">{{ number_format($data['totals']['sales']) }}</div>
    </div>
    <div class="scard">
        <div class="scard-icon bg-blue-100">💰</div>
        <div class="scard-label">Jami summa</div>
        <div class="scard-val text-blue-600">{{ fmt_sum($data['totals']['sum']) }}</div>
    </div>
</div>
