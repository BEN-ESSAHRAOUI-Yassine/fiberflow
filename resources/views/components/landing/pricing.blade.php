<section id="pricing" class="py-24 lg:py-32 bg-surface-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 ff-animate-in" data-direction="fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-xs font-semibold text-brand-700 uppercase tracking-wider mb-4">
                Pricing
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">
                Simple, transparent pricing
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                Start free. Upgrade when you're ready. No hidden fees.
            </p>
        </div>

        {{-- Pricing Cards --}}
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto items-start">

            {{-- Starter --}}
            <div class="ff-pricing-card ff-animate-in" data-direction="fade-up" data-delay="1">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Starter</h3>
                    <p class="text-sm text-gray-500 mt-1">For individual engineers</p>
                </div>
                <div class="flex items-baseline gap-1 mb-6">
                    <span class="text-4xl font-bold text-gray-900">29</span>
                    <span class="text-lg font-medium text-gray-500">&euro;/month</span>
                </div>
                <a href="{{ route('register') }}" class="ff-btn-secondary w-full justify-center mb-8">
                    Start Free Trial
                </a>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        5 Projects
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        AI Audits
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        PDF Reports
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Email Support
                    </li>
                </ul>
            </div>

            {{-- Professional (Highlighted) --}}
            <div class="ff-pricing-highlight ff-animate-in" data-direction="fade-up" data-delay="2">
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                    <span class="inline-block px-4 py-1 rounded-full bg-brand-700 text-white text-xs font-semibold tracking-wide shadow-lg shadow-brand-700/25">
                        Most Popular
                    </span>
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Professional</h3>
                    <p class="text-sm text-gray-500 mt-1">For growing teams</p>
                </div>
                <div class="flex items-baseline gap-1 mb-6">
                    <span class="text-4xl font-bold text-gray-900">79</span>
                    <span class="text-lg font-medium text-gray-500">&euro;/month</span>
                </div>
                <a href="{{ route('register') }}" class="ff-btn-primary w-full justify-center mb-8 shadow-lg shadow-brand-700/25">
                    Start Free Trial
                </a>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Unlimited Projects
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Advanced AI Analysis
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Team Collaboration
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Priority Support
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Excel Exports
                    </li>
                </ul>
            </div>

            {{-- Enterprise --}}
            <div class="ff-pricing-card ff-animate-in" data-direction="fade-up" data-delay="3">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Enterprise</h3>
                    <p class="text-sm text-gray-500 mt-1">For large organizations</p>
                </div>
                <div class="flex items-baseline gap-1 mb-6">
                    <span class="text-4xl font-bold text-gray-900">Custom</span>
                </div>
                <a href="mailto:sales@fiberflow.io" class="ff-btn-secondary w-full justify-center mb-8">
                    Contact Sales
                </a>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Unlimited Everything
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Dedicated Support
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Custom Deployment
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        API Access
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        SSO & SAML
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
