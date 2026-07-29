<section class="py-24 lg:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 ff-animate-in" data-direction="fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-xs font-semibold text-brand-700 uppercase tracking-wider mb-4">
                Testimonials
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">
                Loved by fiber engineers
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                Hear from teams who transformed their audit workflow.
            </p>
        </div>

        {{-- Testimonial Grid --}}
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">

            {{-- Testimonial 1 --}}
            <div class="ff-testimonial-card ff-animate-in" data-direction="fade-up" data-delay="1">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    "FiberFlow cut our audit time from two weeks to three days. The AI anomaly detection catches issues we used to miss entirely. It's become essential for our FTTH deployments."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-semibold text-sm">
                        MR
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Marc Renard</div>
                        <div class="text-xs text-gray-500">Lead Network Engineer</div>
                    </div>
                </div>
            </div>

            {{-- Testimonial 2 --}}
            <div class="ff-testimonial-card ff-animate-in" data-direction="fade-up" data-delay="2">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    "The centralized workspace changed how our team collaborates. No more sending Excel files back and forth. Everyone sees the same data in real time."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-semibold text-sm">
                        SD
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Sophie Dubois</div>
                        <div class="text-xs text-gray-500">Infrastructure Project Manager</div>
                    </div>
                </div>
            </div>

            {{-- Testimonial 3 --}}
            <div class="ff-testimonial-card ff-animate-in" data-direction="fade-up" data-delay="3">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    "The report generation alone saves us 20 hours per project. Professional PDFs with quality scores and anomaly maps — ready in seconds."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-semibold text-sm">
                        TP
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Thomas Perrin</div>
                        <div class="text-xs text-gray-500">Telecom Quality Auditor</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
