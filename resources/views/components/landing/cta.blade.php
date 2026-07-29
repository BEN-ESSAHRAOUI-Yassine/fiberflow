<section class="py-24 lg:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="ff-cta-gradient p-12 md:p-16 text-center relative z-10 ff-animate-in" data-direction="scale-in">
            <h2 class="text-3xl md:text-4xl font-bold text-white tracking-tight mb-4">
                Ready to transform your FTTH audits?
            </h2>
            <p class="text-lg text-white/70 max-w-xl mx-auto mb-10">
                Join engineering teams who audit fiber networks smarter, faster, and more accurately.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-white text-brand-700 font-semibold text-sm rounded-xl hover:bg-white/90 transition-all duration-200 shadow-lg">
                        {{ __('Go to Dashboard') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-white text-brand-700 font-semibold text-sm rounded-xl hover:bg-white/90 transition-all duration-200 shadow-lg">
                        {{ __('Start Free Trial') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="mailto:sales@fiberflow.io" class="inline-flex items-center gap-2 px-7 py-3.5 bg-white/10 text-white font-semibold text-sm rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-200 backdrop-blur-sm">
                        Contact Sales
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>
