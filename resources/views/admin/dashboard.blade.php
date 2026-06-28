@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Foydalanuvchilar</p>
        <p class="text-4xl font-bold text-gray-900 mt-1">{{ $totalUsers }}</p>
        <div class="mt-3 w-10 h-1 bg-emerald-500 rounded"></div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">E'lonlar</p>
        <p class="text-4xl font-bold text-gray-900 mt-1">{{ $totalProducts }}</p>
        <div class="mt-3 w-10 h-1 bg-blue-500 rounded"></div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 font-medium">Murojatlar</p>
        <p class="text-4xl font-bold text-gray-900 mt-1">{{ $totalContacts }}</p>
        <div class="mt-3 w-10 h-1 bg-amber-500 rounded"></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent contacts --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">So'nggi murojatlar</h2>
            <a href="{{ route('admin.contacts') }}" class="text-emerald-600 text-sm font-semibold hover:underline">Hammasi →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentContacts as $c)
                <div class="px-6 py-3">
                    <p class="font-semibold text-sm text-gray-800">{{ $c->name }}</p>
                    <p class="text-xs text-gray-500">{{ $c->phone }} · {{ $c->created_at->diffForHumans() }}</p>
                    @if($c->message)
                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $c->message }}</p>
                    @endif
                </div>
            @empty
                <p class="px-6 py-4 text-sm text-gray-400">Hali murojat yo'q.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent users --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">So'nggi foydalanuvchilar</h2>
            <a href="{{ route('admin.users') }}" class="text-emerald-600 text-sm font-semibold hover:underline">Hammasi →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recentUsers as $u)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ $u->first_name }} {{ $u->last_name }}</p>
                        <p class="text-xs text-gray-400">{{ $u->phone }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-semibold
                        {{ $u->role?->slug === 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $u->role?->name ?? '—' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
