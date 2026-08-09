<section id="product-preview" class="py-24 lg:py-32 bg-surface-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 ff-animate-in" data-direction="fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-xs font-semibold text-brand-700 uppercase tracking-wider mb-4">
                Product Preview
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">
                Built for engineers, by engineers
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                A clean, focused interface that puts your network data first.
            </p>
        </div>

        {{-- Mockup --}}
        <div class="ff-mockup ff-animate-in max-w-5xl mx-auto" data-direction="scale-in" data-delay="1">
            {{-- Browser Bar --}}
            <div class="ff-mockup-bar">
                <div class="flex items-center gap-1.5">
                    <div class="ff-mockup-dot bg-red-400"></div>
                    <div class="ff-mockup-dot bg-amber-400"></div>
                    <div class="ff-mockup-dot bg-emerald-400"></div>
                </div>
                <div class="flex-1 mx-4">
                    <div class="flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg bg-surface-100 text-xs text-gray-400 font-medium max-w-md mx-auto">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
                        app.fiberflow.io/projects/ree-lyon-nord/audits
                    </div>
                </div>
                <div class="w-16"></div>
            </div>

            {{-- Dashboard Content --}}
            <div class="flex">
                {{-- Sidebar --}}
                <div class="hidden md:flex w-56 bg-surface-50 border-r border-surface-200 p-4 flex-col gap-1">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-brand-50 text-brand-700 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </div>
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-500 text-sm font-medium hover:bg-surface-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Network Map
                    </div>
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-500 text-sm font-medium hover:bg-surface-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Audits
                    </div>
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-500 text-sm font-medium hover:bg-surface-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Reports
                    </div>
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-500 text-sm font-medium hover:bg-surface-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Settings
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="flex-1 p-6 bg-white min-h-[400px]">
                    {{-- Page Title --}}
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                                <span>Projects</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                <span>REE Lyon Nord</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                <span class="text-gray-700">Audit #12</span>
                            </div>
                            <h1 class="text-xl font-bold text-gray-900">Network Audit Report</h1>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">Score: 94.3</div>
                            <div class="px-3 py-1.5 rounded-lg bg-surface-100 text-gray-600 text-xs font-medium border border-surface-200">Export</div>
                        </div>
                    </div>

                    {{-- Map + Stats --}}
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        {{-- Map --}}
                        <div class="col-span-2 h-48 rounded-xl bg-surface-50 border border-surface-200 relative overflow-hidden">
                            <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="mock-grid" width="24" height="24" patternUnits="userSpaceOnUse">
                                        <path d="M 24 0 L 0 0 0 24" fill="none" stroke="rgba(24,68,216,0.05)" stroke-width="1"/>
                                    </pattern>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#mock-grid)"/>
                            </svg>
                            <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                <path d="M30 140 L90 90 L150 110 L210 60 L270 80 L330 50" stroke="#3B6CFF" stroke-width="2" fill="none" opacity="0.5"/>
                                <path d="M30 120 L80 100 L140 120 L200 70 L260 90 L320 60" stroke="#6B94FF" stroke-width="1.5" fill="none" opacity="0.3"/>
                                <path d="M90 90 L100 50" stroke="#3B6CFF" stroke-width="1.5" fill="none" opacity="0.3"/>
                                <path d="M150 110 L160 150" stroke="#3B6CFF" stroke-width="1.5" fill="none" opacity="0.3"/>
                                <path d="M210 60 L220 30" stroke="#3B6CFF" stroke-width="1.5" fill="none" opacity="0.3"/>
                                <circle cx="30" cy="140" r="4" fill="#1844D8"/>
                                <circle cx="90" cy="90" r="4" fill="#1844D8"/>
                                <circle cx="150" cy="110" r="5" fill="#F59E0B"/>
                                <circle cx="150" cy="110" r="9" fill="none" stroke="#F59E0B" stroke-width="1.5" opacity="0.5"/>
                                <circle cx="210" cy="60" r="4" fill="#1844D8"/>
                                <circle cx="270" cy="80" r="4" fill="#1844D8"/>
                                <circle cx="330" cy="50" r="4" fill="#1844D8"/>
                                <circle cx="100" cy="50" r="3" fill="#3B6CFF" opacity="0.6"/>
                                <circle cx="160" cy="150" r="3" fill="#3B6CFF" opacity="0.6"/>
                                <circle cx="220" cy="30" r="3" fill="#3B6CFF" opacity="0.6"/>
                            </svg>
                            <div class="absolute bottom-2 left-2 flex items-center gap-1.5 px-2 py-1 rounded-md bg-white/90 text-[10px] font-medium text-gray-500 border border-surface-200">
                                <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
                                1 anomaly detected
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="flex flex-col gap-3">
                            <div class="flex-1 p-3 rounded-xl bg-surface-50 border border-surface-200">
                                <div class="text-[10px] text-gray-400 uppercase tracking-wider font-medium mb-1">Total Cables</div>
                                <div class="text-xl font-bold text-gray-900 font-mono">1,247</div>
                                <div class="text-[10px] text-emerald-600 font-medium mt-0.5">+12% from last audit</div>
                            </div>
                            <div class="flex-1 p-3 rounded-xl bg-surface-50 border border-surface-200">
                                <div class="text-[10px] text-gray-400 uppercase tracking-wider font-medium mb-1">Anomalies</div>
                                <div class="text-xl font-bold text-amber-600 font-mono">12</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">3 critical, 9 warnings</div>
                            </div>
                            <div class="flex-1 p-3 rounded-xl bg-surface-50 border border-surface-200">
                                <div class="text-[10px] text-gray-400 uppercase tracking-wider font-medium mb-1">Coverage</div>
                                <div class="text-xl font-bold text-brand-600 font-mono">98.2%</div>
                                <div class="text-[10px] text-emerald-600 font-medium mt-0.5">Target: 95%</div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Row --}}
                    <div class="grid grid-cols-4 gap-3">
                        <div class="p-3 rounded-xl bg-brand-50 border border-brand-200">
                            <div class="text-[10px] text-brand-600 font-medium uppercase tracking-wider">Feeder</div>
                            <div class="text-sm font-bold text-brand-700 mt-1 font-mono">287 cables</div>
                        </div>
                        <div class="p-3 rounded-xl bg-surface-50 border border-surface-200">
                            <div class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Distribution</div>
                            <div class="text-sm font-bold text-gray-900 mt-1 font-mono">654 cables</div>
                        </div>
                        <div class="p-3 rounded-xl bg-surface-50 border border-surface-200">
                            <div class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Drop</div>
                            <div class="text-sm font-bold text-gray-900 mt-1 font-mono">306 cables</div>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                            <div class="text-[10px] text-emerald-600 font-medium uppercase tracking-wider">Quality</div>
                            <div class="text-sm font-bold text-emerald-700 mt-1 font-mono">94.3 / 100</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
