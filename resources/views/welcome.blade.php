<x-app-layout>

{{-- ===== HERO ===== --}}
<section class="relative h-screen overflow-hidden bg-[#011f13]">
    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-[10s] ease-in-out hover:scale-105"
         style="background-image: url('https://images.unsplash.com/photo-1545468800-85cc9bc6ecf7?q=80&w=2070&auto=format&fit=crop')">
    </div>
    <div class="absolute inset-0"
         style="background: linear-gradient(to bottom, rgba(1,31,19,.85) 0%, rgba(1,31,19,.4) 50%, rgba(1,31,19,.9) 100%)">
    </div>

    <div class="relative z-10 h-full flex flex-col justify-center px-[5%] max-w-[900px] text-white">
        <h1 class="font-serif text-5xl lg:text-6xl font-bold leading-tight mb-6 opacity-0 anim-1"
            style="text-shadow: 0 2px 10px rgba(0,0,0,.3)">
            {{ __('welcome.hero_title') }}
        </h1>
        <div class="w-20 h-[3px] bg-emerald-500 mb-6 opacity-0 anim-2"></div>
        <p class="text-xl font-light leading-relaxed max-w-xl opacity-0 anim-3">
            {{ __('welcome.hero_desc') }}
        </p>
    </div>

    <div class="absolute bottom-16 left-0 w-full flex flex-col sm:flex-row justify-center gap-5 z-20 px-5 opacity-0 anim-4">
        <a href="{{ url('/register') }}"
           class="px-12 py-4 bg-emerald-500 text-white rounded-full font-semibold uppercase tracking-widest text-sm
                  hover:bg-[#0e8a60] hover:-translate-y-1 transition-all duration-300 shadow-lg text-center">
            {{ __('welcome.start_selling') }}
        </a>
        <a href="{{ url('/marketplace') }}"
           class="px-12 py-4 bg-white/10 backdrop-blur-sm border-2 border-white/80 text-white rounded-full font-semibold
                  uppercase tracking-widest text-sm hover:bg-white hover:text-[#011f13] hover:-translate-y-1
                  transition-all duration-300 shadow-lg text-center">
            {{ __('welcome.browse_livestock') }}
        </a>
    </div>
</section>

{{-- ===== WHY CHOOSE ===== --}}
<section id="why" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="font-serif text-4xl font-bold text-[#011f13] mb-3">
                {{ __('welcome.why_title') }}
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto">
                {{ __('welcome.why_desc') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="relative h-96 rounded-2xl overflow-hidden shadow-lg group cursor-pointer">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                     style="background-image: url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?q=80&w=2070&auto=format&fit=crop')"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/10
                            group-hover:from-emerald-900/90 group-hover:via-emerald-800/30 transition-all duration-300"></div>
                <div class="absolute bottom-0 left-0 w-full p-8 text-white z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-2xl mb-5 border border-white/30">
                        🤝
                    </div>
                    <h3 class="text-2xl font-bold mb-2">{{ __('welcome.trust_title') }}</h3>
                    <p class="text-sm opacity-75">{{ __('welcome.trust_desc') }}</p>
                </div>
            </div>

            <div class="relative h-96 rounded-2xl overflow-hidden shadow-lg group cursor-pointer">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                     style="background-image: url('https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=2021&auto=format&fit=crop')"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/10
                            group-hover:from-emerald-900/90 group-hover:via-emerald-800/30 transition-all duration-300"></div>
                <div class="absolute bottom-0 left-0 w-full p-8 text-white z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-2xl mb-5 border border-white/30">
                        🚚
                    </div>
                    <h3 class="text-2xl font-bold mb-2">{{ __('welcome.delivery_title') }}</h3>
                    <p class="text-sm opacity-75">{{ __('welcome.delivery_desc') }}</p>
                </div>
            </div>

            <div class="relative h-96 rounded-2xl overflow-hidden shadow-lg group cursor-pointer">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                     style="background-image: url('https://images.unsplash.com/photo-1545468800-85cc9bc6ecf7?q=80&w=2070&auto=format&fit=crop')"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/10
                            group-hover:from-emerald-900/90 group-hover:via-emerald-800/30 transition-all duration-300"></div>
                <div class="absolute bottom-0 left-0 w-full p-8 text-white z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-2xl mb-5 border border-white/30">
                        🏷️
                    </div>
                    <h3 class="text-2xl font-bold mb-2">{{ __('welcome.price_title') }}</h3>
                    <p class="text-sm opacity-75">{{ __('welcome.price_desc') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== HOW IT WORKS ===== --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="font-serif text-4xl font-bold text-[#011f13]">{{ __('welcome.how_title') }}</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 text-center">
            <div class="group flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center text-2xl mb-5
                            shadow-lg shadow-emerald-200 group-hover:-translate-y-2 group-hover:bg-emerald-600 transition-all duration-300">
                    👤
                </div>
                <h3 class="text-lg font-bold mb-2">{{ __('welcome.step1_title') }}</h3>
                <p class="text-gray-500 text-sm">{{ __('welcome.step1_desc') }}</p>
            </div>
            <div class="group flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center text-2xl mb-5
                            shadow-lg shadow-emerald-200 group-hover:-translate-y-2 group-hover:bg-emerald-600 transition-all duration-300">
                    🔍
                </div>
                <h3 class="text-lg font-bold mb-2">{{ __('welcome.step2_title') }}</h3>
                <p class="text-gray-500 text-sm">{{ __('welcome.step2_desc') }}</p>
            </div>
            <div class="group flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center text-2xl mb-5
                            shadow-lg shadow-emerald-200 group-hover:-translate-y-2 group-hover:bg-emerald-600 transition-all duration-300">
                    💬
                </div>
                <h3 class="text-lg font-bold mb-2">{{ __('welcome.step3_title') }}</h3>
                <p class="text-gray-500 text-sm">{{ __('welcome.step3_desc') }}</p>
            </div>
            <div class="group flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center text-2xl mb-5
                            shadow-lg shadow-emerald-200 group-hover:-translate-y-2 group-hover:bg-emerald-600 transition-all duration-300">
                    ✅
                </div>
                <h3 class="text-lg font-bold mb-2">{{ __('welcome.step4_title') }}</h3>
                <p class="text-gray-500 text-sm">{{ __('welcome.step4_desc') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== STAY CONNECTED ===== --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-serif text-4xl font-bold text-[#011f13] text-center mb-10">{{ __('welcome.stay_connected') }}</h2>

        <div class="flex flex-col md:flex-row gap-5" style="height: auto; min-height: 320px;">
            <div class="flex-1 relative overflow-hidden rounded-2xl cursor-pointer group" style="min-height: 300px;">
                <img src="https://images.pexels.com/photos/1108099/pexels-photo-1108099.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="Livestock">
                <div class="absolute inset-0 bg-[#011f13]/75 text-white flex flex-col justify-center p-8
                            opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <p class="text-lg mb-4">"Veterinary verification has never been faster. Our new digital health records are live."</p>
                    <span class="text-sm opacity-75">@CHORVAI</span>
                </div>
            </div>

            <div class="flex-1 bg-[#011f13] rounded-2xl flex flex-col justify-center p-8 text-white" style="min-height: 300px;">
                <p class="text-lg mb-4">"When Little Dove sat down mid-walk, her owner knew something was off. A swift response from our vet team saved the day."</p>
                <span class="text-sm opacity-75">@CHORVAI</span>
            </div>

            <div class="flex-1 relative overflow-hidden rounded-2xl cursor-pointer group" style="min-height: 300px;">
                <img src="https://images.pexels.com/photos/4207906/pexels-photo-4207906.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="Farmer">
                <div class="absolute inset-0 bg-[#011f13]/75 text-white flex flex-col justify-center p-8
                            opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <p class="text-lg mb-4">"Empowering the next generation of agricultural entrepreneurs through technology."</p>
                    <span class="text-sm opacity-75">@CHORVAI</span>
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-8 mt-10 text-gray-400 text-2xl">
            <a href="#" class="hover:text-emerald-500 transition-colors">📸</a>
            <a href="#" class="hover:text-emerald-500 transition-colors">📘</a>
            <a href="#" class="hover:text-emerald-500 transition-colors">▶️</a>
            <a href="#" class="hover:text-emerald-500 transition-colors">💼</a>
        </div>
    </div>
</section>

{{-- ===== CONTACT ===== --}}
<section id="contact" class="py-20" style="background:#011f13">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-start">

            {{-- Left: info --}}
            <div class="text-white">
                <h2 class="font-serif text-4xl font-bold mb-4">{{ __('welcome.contact_title') }}</h2>
                <p class="text-white/70 text-lg leading-relaxed mb-10">{{ __('welcome.contact_desc') }}</p>

                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center text-xl">📞</div>
                        <div>
                            <p class="text-white/50 text-sm">{{ __('nav.contact') }}</p>
                            <p class="text-white font-semibold">+998 90 000 00 00</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center text-xl">✉️</div>
                        <div>
                            <p class="text-white/50 text-sm">Email</p>
                            <p class="text-white font-semibold">info@chorvaai.uz</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center text-xl">🕐</div>
                        <div>
                            <p class="text-white/50 text-sm">{{ __('welcome.contact_info_title') }}</p>
                            <p class="text-white font-semibold">{{ __('welcome.contact_working_hours') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: form --}}
            <div class="bg-white rounded-2xl p-8 shadow-2xl">
                @if(session('contact_success'))
                    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 mb-6">
                        <span class="text-2xl">✅</span>
                        <p class="font-semibold">{{ __('welcome.contact_success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            {{ __('welcome.contact_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Abdullayev Abdulloh"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            {{ __('welcome.contact_phone') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                               placeholder="+998 90 123 45 67"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none @error('phone') border-red-400 @enderror">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            {{ __('welcome.contact_message') }}
                        </label>
                        <textarea name="message" rows="4"
                                  placeholder="..."
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none resize-none">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition text-sm tracking-wide">
                        {{ __('welcome.contact_send') }}
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="relative py-24 overflow-hidden" style="background: #064e3b;">
    <div class="absolute inset-0 opacity-10"
         style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 20px 20px;">
    </div>
    <div class="relative z-10 max-w-3xl mx-auto text-center px-4 text-white">
        <h2 class="font-serif text-4xl font-bold mb-4">{{ __('welcome.cta_title') }}</h2>
        <p class="text-white/75 text-xl mb-10">{{ __('welcome.cta_desc') }}</p>
        @auth
            <a href="{{ url('/marketplace') }}"
               class="inline-block px-12 py-4 bg-white text-emerald-800 rounded-full font-bold text-sm uppercase
                      tracking-widest hover:-translate-y-1 transition-transform duration-300 shadow-lg">
                {{ __('welcome.go_to_marketplace') }}
            </a>
        @else
            <a href="{{ url('/register') }}"
               class="inline-block px-12 py-4 bg-emerald-500 text-white rounded-full font-bold text-sm uppercase
                      tracking-widest hover:bg-emerald-400 hover:-translate-y-1 transition-all duration-300 shadow-lg">
                {{ __('welcome.get_started') }}
            </a>
        @endauth
    </div>
</section>

</x-app-layout>
