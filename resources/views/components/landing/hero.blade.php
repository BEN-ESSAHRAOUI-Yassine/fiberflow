<section id="hero" class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
    {{-- Background --}}
    <div class="absolute inset-0 landing-grid"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-gradient-to-b from-brand-100/40 via-brand-50/20 to-transparent rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            {{-- Left: Content --}}
            <div class="max-w-2xl">
                {{-- Badge --}}
                <div class="ff-animate-in inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 border border-brand-200 text-sm font-medium text-brand-700 mb-8" data-direction="fade-up">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    AI-Powered FTTH Auditing
                </div>

                {{-- Headline --}}
                <h1 class="ff-hero-title ff-animate-in" data-direction="fade-up" data-delay="1">
                    Smarter FTTH Audits.<br>
                    <span>Powered by AI.</span>
                </h1>

                {{-- Subheadline --}}
                <p class="ff-hero-sub ff-animate-in mt-6" data-direction="fade-up" data-delay="2">
                    FiberFlow helps engineering teams detect infrastructure anomalies, analyze optical networks, and generate intelligent audit reports — faster than ever.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-wrap items-center gap-4 mt-10 ff-animate-in" data-direction="fade-up" data-delay="3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-brand-700 text-white font-semibold text-sm rounded-xl hover:bg-brand-800 transition-all duration-200 shadow-lg shadow-brand-700/25 hover:shadow-xl hover:shadow-brand-700/30">
                            {{ __('Go to Dashboard') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-brand-700 text-white font-semibold text-sm rounded-xl hover:bg-brand-800 transition-all duration-200 shadow-lg shadow-brand-700/25 hover:shadow-xl hover:shadow-brand-700/30">
                            {{ __('Start Free Trial') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="#product-preview" class="inline-flex items-center gap-2 px-7 py-3.5 bg-white text-gray-700 font-semibold text-sm rounded-xl border border-surface-200 hover:border-surface-300 hover:bg-surface-50 transition-all duration-200 shadow-surface">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('Watch Demo') }}
                        </a>
                    @endauth
                </div>

                {{-- Trust signals --}}
                <div class="flex items-center gap-6 mt-10 ff-animate-in" data-direction="fade-up" data-delay="4">
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        No credit card required
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        14-day free trial
                    </div>
                </div>
            </div>

            {{-- Right: Illustration --}}
            <div class="relative hidden lg:block">
                {{-- Floating Badges --}}
                <div class="absolute -top-4 left-8 z-20 ff-float-badge animate-float">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    AI Copilot
                </div>
                <div class="absolute top-1/2 -right-4 z-20 ff-float-badge animate-float-delayed">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Smart Audit
                </div>
                <div class="absolute -bottom-2 left-1/3 z-20 ff-float-badge animate-float-slow">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Instant Reports
                </div>

                {{-- Main Illustration Card --}}
                <div class="relative bg-white rounded-3xl border border-surface-200 shadow-surface-lg p-6 ml-8">
                    {{-- Mini Map --}}
                    <div class="w-full h-56 rounded-2xl bg-gradient-to-br from-surface-50 to-surface-100 border border-surface-200 relative overflow-hidden mb-4">
                        {{-- Grid lines --}}
                        <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern id="hero-grid" width="30" height="30" patternUnits="userSpaceOnUse">
                                    <path d="M 30 0 L 0 0 0 30" fill="none" stroke="rgba(24,68,216,0.06)" stroke-width="1"/>
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#hero-grid)"/>
                        </svg>
                        {{-- Fiber paths --}}
                        <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
                            <path d="M40 180 L120 120 L200 140 L280 80 L360 100" stroke="#3B6CFF" stroke-width="2" fill="none" opacity="0.4"/>
                            <path d="M40 160 L100 140 L180 160 L260 100 L340 120" stroke="#6B94FF" stroke-width="1.5" fill="none" opacity="0.3"/>
                            <path d="M120 120 L140 60" stroke="#3B6CFF" stroke-width="1.5" fill="none" opacity="0.3"/>
                            <path d="M200 140 L220 200" stroke="#3B6CFF" stroke-width="1.5" fill="none" opacity="0.3"/>
                            {{-- Nodes --}}
                            <circle cx="40" cy="180" r="5" fill="#1844D8"/>
                            <circle cx="120" cy="120" r="5" fill="#1844D8"/>
                            <circle cx="200" cy="140" r="5" fill="#3B6CFF"/>
                            <circle cx="280" cy="80" r="5" fill="#1844D8"/>
                            <circle cx="360" cy="100" r="5" fill="#1844D8"/>
                            <circle cx="140" cy="60" r="4" fill="#3B6CFF" opacity="0.7"/>
                            <circle cx="220" cy="200" r="4" fill="#3B6CFF" opacity="0.7"/>
                            {{-- Anomaly marker --}}
                            <circle cx="200" cy="140" r="10" fill="none" stroke="#F59E0B" stroke-width="2" opacity="0.6"/>
                            <circle cx="200" cy="140" r="16" fill="none" stroke="#F59E0B" stroke-width="1" opacity="0.3"/>
                        </svg>
                        {{-- Map label --}}
                        <div class="absolute top-3 left-3 flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/90 backdrop-blur-sm border border-surface-200 text-xs font-medium text-gray-600 shadow-surface">
                            <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            Network Map
                        </div>
                    </div>

                    {{-- Mini Stats --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3 rounded-xl bg-surface-50 border border-surface-200">
                            <div class="text-xs text-gray-500 mb-1">Quality Score</div>
                            <div class="text-lg font-bold text-emerald-600">94.3</div>
                        </div>
                        <div class="p-3 rounded-xl bg-surface-50 border border-surface-200">
                            <div class="text-xs text-gray-500 mb-1">Audits</div>
                            <div class="text-lg font-bold text-gray-900">47</div>
                        </div>
                        <div class="p-3 rounded-xl bg-surface-50 border border-surface-200">
                            <div class="text-xs text-gray-500 mb-1">Anomalies</div>
                            <div class="text-lg font-bold text-amber-600">12</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
