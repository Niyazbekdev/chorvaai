@extends('admin.layout')
@section('title', 'Murojatlar')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-800">Barcha murojatlar ({{ $contacts->total() }})</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left">Ism</th>
                    <th class="px-6 py-3 text-left">Telefon</th>
                    <th class="px-6 py-3 text-left">Xabar</th>
                    <th class="px-6 py-3 text-left">Sana</th>
                    <th class="px-6 py-3 text-left">Amal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($contacts as $contact)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-semibold text-gray-800">{{ $contact->name }}</td>
                        <td class="px-6 py-3">
                            <a href="tel:{{ $contact->phone }}"
                               class="text-emerald-600 font-semibold hover:underline">{{ $contact->phone }}</a>
                        </td>
                        <td class="px-6 py-3 text-gray-500 max-w-[300px]">
                            {{ $contact->message ?: '—' }}
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">
                            {{ $contact->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('admin.contacts.delete', $contact) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm(\"O'chirishni tasdiqlaysizmi?\")"
                                        class="text-xs text-red-500 hover:text-red-700 font-semibold">
                                    O'chirish
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">Hali murojat yo'q.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contacts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $contacts->links() }}</div>
    @endif
</div>
@endsection
