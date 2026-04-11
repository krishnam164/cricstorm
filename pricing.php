<?php include 'header.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<section class="relative py-12 md:py-20 px-4 md:px-6 bg-gradient-to-b from-teal-50/50 to-primary overflow-hidden text-center">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] md:w-[800px] h-[300px] md:h-[400px] bg-brand/5 rounded-full blur-3xl -z-10 animate__animated animate__pulse animate__infinite animate__slow"></div>
    
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl md:text-6xl font-black text-slate-900 mb-4 md:mb-6 leading-tight animate__animated animate__fadeInDown px-2">
            Flexible <span class="text-brand">Pricing</span> for <br>Every Tournament.
        </h1>
        <p class="text-slate-500 text-base md:text-xl font-medium max-w-2xl mx-auto animate__animated animate__fadeIn animate__delay-1s px-4">
            Choose the plan that fits your league size. No hidden fees, just pure cricket auction excitement.
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-6 lg:gap-8">
        
        <div class="bg-white p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] border border-teal-50 flex flex-col hover:shadow-xl transition-all duration-500 group animate__animated animate__fadeInLeft">
            <div class="mb-6 md:mb-8">
                <h3 class="text-xl font-black text-slate-900 mb-2">Starter</h3>
                <p class="text-slate-400 text-sm">Perfect for local friendly matches.</p>
            </div>
            <div class="mb-6 md:mb-8 group-hover:scale-105 transition-transform">
                <span class="text-4xl md:text-5xl font-black text-slate-900">₹999</span>
                <span class="text-slate-400 font-bold uppercase text-[10px]">/ Tournament</span>
            </div>
            <ul class="space-y-4 mb-10 flex-grow">
                <li class="flex items-center text-slate-600 text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Up to 4 Teams
                </li>
                <li class="flex items-center text-slate-600 text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Up to 60 Players
                </li>
                <li class="flex items-center text-slate-600 text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Basic Dashboard
                </li>
                <li class="flex items-center text-slate-400 text-sm line-through">
                    <i class="fas fa-times mr-3"></i> Live Streaming Overlay
                </li>
            </ul>
            <a href="contact_us.php" class="block text-center py-4 rounded-2xl border-2 border-slate-900 text-slate-900 font-black hover:bg-slate-900 hover:text-white transition-all active:scale-95 text-sm">
                CHOOSE STARTER
            </a>
        </div>

        <div class="bg-slate-900 p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] flex flex-col shadow-2xl shadow-brand/20 relative transform md:-translate-y-6 mt-6 md:mt-0 animate__animated animate__fadeInUp group">
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-brand text-white text-[9px] md:text-[10px] font-black px-5 md:px-6 py-2 rounded-full uppercase tracking-widest animate-bounce whitespace-nowrap">
                Most Popular
            </div>
            <div class="mb-6 md:mb-8 mt-2">
                <h3 class="text-xl font-black text-white mb-2">Professional</h3>
                <p class="text-slate-400 text-sm">Everything you need for a major league.</p>
            </div>
            <div class="mb-6 md:mb-8 group-hover:scale-105 transition-transform duration-500">
                <span class="text-4xl md:text-5xl font-black text-white">₹2,499</span>
                <span class="text-slate-400 font-bold uppercase text-[10px]">/ Tournament</span>
            </div>
            <ul class="space-y-4 mb-10 flex-grow text-white/80">
                <li class="flex items-center text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Up to 12 Teams
                </li>
                <li class="flex items-center text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Unlimited Players
                </li>
                <li class="flex items-center text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> HD Streaming Overlays
                </li>
                <li class="flex items-center text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Owner Dashboards
                </li>
                <li class="flex items-center text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Real-time Support
                </li>
            </ul>
            <a href="contact_us.php" class="block text-center py-4 rounded-2xl bg-brand text-white font-black hover:bg-white hover:text-brand transition-all duration-300 shadow-xl shadow-brand/30 hover:scale-105 active:scale-95 text-sm">
                GO PROFESSIONAL
            </a>
        </div>

        <div class="bg-white p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] border border-teal-50 flex flex-col hover:shadow-xl transition-all duration-500 mt-6 md:mt-0 animate__animated animate__fadeInRight group">
            <div class="mb-6 md:mb-8">
                <h3 class="text-xl font-black text-slate-900 mb-2">Custom</h3>
                <p class="text-slate-400 text-sm">For massive events and organizers.</p>
            </div>
            <div class="mb-6 md:mb-8 group-hover:scale-105 transition-transform">
                <span class="text-3xl md:text-4xl font-black text-slate-900">Contact</span>
                <span class="text-slate-400 font-bold uppercase text-[10px]">/ For Quote</span>
            </div>
            <ul class="space-y-4 mb-10 flex-grow">
                <li class="flex items-center text-slate-600 text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Multi-Tournaments
                </li>
                <li class="flex items-center text-slate-600 text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> Custom Branding
                </li>
                <li class="flex items-center text-slate-600 text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> White-label Domain
                </li>
                <li class="flex items-center text-slate-600 text-sm">
                    <i class="fas fa-check text-brand mr-3"></i> On-site Support
                </li>
            </ul>
            <a href="contact_us.php" class="block text-center py-4 rounded-2xl border-2 border-slate-900 text-slate-900 font-black hover:bg-slate-900 hover:text-white transition-all active:scale-95 text-sm">
                CONTACT SALES
            </a>
        </div>

    </div>
</section>

<section class="max-w-3xl mx-auto px-6 pb-16 md:pb-24 text-center">
    <h4 class="text-xl md:text-2xl font-black text-slate-900 mb-8 animate__animated animate__fadeIn">Frequently Asked Questions</h4>
    <div class="space-y-4 md:space-y-6 text-left">
        <div class="p-5 md:p-6 bg-white rounded-xl md:rounded-2xl border border-teal-50 hover:border-brand/30 transition-colors group">
            <h5 class="font-bold text-slate-900 mb-2 group-hover:text-brand transition-colors text-sm md:text-base">Is there a free trial?</h5>
            <p class="text-slate-500 text-xs md:text-sm">Yes, you can set up a "Demo Tournament" for free to test all the features before paying.</p>
        </div>
        <div class="p-5 md:p-6 bg-white rounded-xl md:rounded-2xl border border-teal-50 hover:border-brand/30 transition-colors group">
            <h5 class="font-bold text-slate-900 mb-2 group-hover:text-brand transition-colors text-sm md:text-base">Can I upgrade my plan later?</h5>
            <p class="text-slate-500 text-xs md:text-sm">Absolutely. You can start with Starter and upgrade to Professional as your player list grows.</p>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>