@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm bg-gray-50 focus:bg-white transition']) }}>
