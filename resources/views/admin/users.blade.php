@extends('admin.layout')
@section('title', 'Foydalanuvchilar')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-800">Barcha foydalanuvchilar ({{ $users->total() }})</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left">Ism</th>
                    <th class="px-6 py-3 text-left">Telefon</th>
                    <th class="px-6 py-3 text-left">Rol</th>
                    <th class="px-6 py-3 text-left">Ro'yxatdan o'tgan</th>
                    <th class="px-6 py-3 text-left">Amal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-semibold text-gray-800">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $user->phone }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                {{ $user->role?->slug === 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->role?->name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d.m.Y') }}</td>
                        <td class="px-6 py-3">
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role_id"
                                            class="text-xs border border-gray-200 rounded-lg px-2 py-1 focus:ring-1 focus:ring-emerald-500 outline-none">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="text-xs bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded-lg font-semibold transition">
                                        Saqlash
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-300">Siz</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>
@endsection
