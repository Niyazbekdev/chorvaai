@extends('admin.layout')
@section('title', "E'lonlar")

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-800">Barcha e'lonlar ({{ $products->total() }})</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left">Nomi</th>
                    <th class="px-6 py-3 text-left">Kategoriya</th>
                    <th class="px-6 py-3 text-left">Narx</th>
                    <th class="px-6 py-3 text-left">Sotuvchi</th>
                    <th class="px-6 py-3 text-left">Sana</th>
                    <th class="px-6 py-3 text-left">Amal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($products as $product)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-semibold text-gray-800 max-w-[200px] truncate">
                            {{ $product->name }}
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-emerald-600 font-bold">{{ $product->formatted_price }}</td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ $product->user?->first_name }} {{ $product->user?->last_name }}
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $product->created_at->format('d.m.Y') }}</td>
                        <td class="px-6 py-3">
                            <a href="{{ route('products.show', $product) }}"
                               target="_blank"
                               class="text-xs text-blue-600 hover:underline font-semibold">Ko'rish →</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $products->links() }}</div>
    @endif
</div>
@endsection
