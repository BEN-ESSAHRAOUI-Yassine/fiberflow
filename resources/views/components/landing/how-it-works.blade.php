<section id="how-it-works" class="py-24 lg:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 ff-animate-in" data-direction="fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-xs font-semibold text-brand-700 uppercase tracking-wider mb-4">
                How It Works
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">
                From data to insights in four steps
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                A streamlined workflow designed for engineering teams.
            </p>
        </div>

        {{-- Steps --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 relative">
            {{-- Connector line (desktop) --}}
            <div class="hidden lg:block absolute top-9 left-[12.5%] right-[12.5%] h-[2px] bg-gradient-to-r from-brand-200 via-brand-400 to-brand-200 opacity-50"></div>

            {{-- Step 1 --}}
            <div class="ff-step-card ff-animate-in relative" data-direction="fade-up" data-delay="1">
                <div class="ff-step-number">1</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Create your project</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Set up your FTTH study with project details, geographic scope, and team access.
                </p>
            </div>

            {{-- Step 2 --}}
            <div class="ff-step-card ff-animate-in relative" data-direction="fade-up" data-delay="2">
                <div class="ff-step-number">2</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Import your network</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Upload GeoJSON data. FiberFlow automatically parses cables, supports, and infrastructure.
                </p>
            </div>

            {{-- Step 3 --}}
            <div class="ff-step-card ff-animate-in relative" data-direction="fade-up" data-delay="3">
                <div class="ff-step-number">3</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Run AI Audit</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    AI analyzes your network for anomalies, compliance issues, and quality scoring.
                </p>
            </div>

            {{-- Step 4 --}}
            <div class="ff-step-card ff-animate-in relative" data-direction="fade-up" data-delay="4">
                <div class="ff-step-number">4</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Review & export</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Download professional PDF reports or Excel exports for compliance and stakeholder review.
                </p>
            </div>
        </div>
    </div>
</section>
