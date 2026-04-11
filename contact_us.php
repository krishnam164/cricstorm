<?php include 'header.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<section class="relative py-12 md:py-20 px-4 md:px-6 bg-gradient-to-b from-teal-50/50 to-primary overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] md:w-[800px] h-[300px] md:h-[400px] bg-brand/5 rounded-full blur-3xl -z-10 animate__animated animate__pulse animate__infinite animate__slow"></div>
    
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl md:text-6xl font-black text-slate900 mb-4 md:mb-6 leading-tight animate__animated animate__fadeInDown">
            Get in <span class="text-brand">Touch.</span>
        </h1>
        <p class="text-slate-500 text-base md:text-xl font-medium max-w-2xl mx-auto animate__animated animate__fadeIn animate__delay-1s px-2">
            Have questions about setting up your auction? Our team is here to help you hit a six.
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">
        
        <div class="space-y-8 md:space-y-12 animate__animated animate__fadeInLeft">
            <div class="text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-black text-slate900 mb-4 md:mb-6 border-l-0 md:border-l-8 border-brand pl-0 md:pl-6">Contact Details</h2>
                <p class="text-slate-500 text-base md:text-lg leading-relaxed">
                    Whether you're a local club or a professional league, we provide the support you need to run a seamless auction.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:gap-8">
                <div class="flex items-center space-x-4 md:space-x-6 p-5 md:p-6 bg-white rounded-2xl md:rounded-3xl border border-teal-50 brand-glow hover:-translate-y-1 md:hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-12 h-12 md:w-14 md:h-14 bg-brand text-white rounded-xl md:rounded-2xl flex-shrink-0 flex items-center justify-center text-lg md:text-xl group-hover:rotate-12 transition-transform">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest">Call Us</h4>
                        <p class="text-base md:text-lg font-bold text-slate-900 truncate">+91 97376 31021</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4 md:space-x-6 p-5 md:p-6 bg-white rounded-2xl md:rounded-3xl border border-teal-50 brand-glow hover:-translate-y-1 md:hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-12 h-12 md:w-14 md:h-14 bg-brand text-white rounded-xl md:rounded-2xl flex-shrink-0 flex items-center justify-center text-lg md:text-xl group-hover:rotate-12 transition-transform">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest">Email Support</h4>
                        <p class="text-base md:text-lg font-bold text-slate-900 truncate">contact@cricstorm.com</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4 md:space-x-6 p-5 md:p-6 bg-white rounded-2xl md:rounded-3xl border border-teal-50 brand-glow hover:-translate-y-1 md:hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-12 h-12 md:w-14 md:h-14 bg-brand text-white rounded-xl md:rounded-2xl flex-shrink-0 flex items-center justify-center text-lg md:text-xl group-hover:rotate-12 transition-transform">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest">Headquarters</h4>
                        <p class="text-base md:text-lg font-bold text-slate-900">Bardoli, Gujarat, India</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 md:p-12 rounded-[2rem] md:rounded-[3rem] shadow-xl border border-teal-50 animate__animated animate__fadeInRight">
            <h3 class="text-xl md:text-2xl font-black text-slate-900 mb-6 md:mb-8">Send a Message</h3>
            
            <form action="process_contact.php" method="POST" class="space-y-5 md:space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Full Name</label>
                        <input type="text" name="name" placeholder="John Doe" 
                               class="w-full px-5 py-3 md:px-6 md:py-4 bg-primary border border-teal-50 rounded-xl md:rounded-2xl focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/5 transition-all text-sm md:text-base">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Phone Number</label>
                        <input type="tel" name="phone" placeholder="+91 00000 00000" 
                               class="w-full px-5 py-3 md:px-6 md:py-4 bg-primary border border-teal-50 rounded-xl md:rounded-2xl focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/5 transition-all text-sm md:text-base">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" 
                           class="w-full px-5 py-3 md:px-6 md:py-4 bg-primary border border-teal-50 rounded-xl md:rounded-2xl focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/5 transition-all text-sm md:text-base">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">Your Message</label>
                    <textarea name="message" rows="4" placeholder="Tell us about your tournament..." 
                              class="w-full px-5 py-3 md:px-6 md:py-4 bg-primary border border-teal-50 rounded-xl md:rounded-2xl focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/5 transition-all resize-none text-sm md:text-base"></textarea>
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white font-black py-4 md:py-5 rounded-xl md:rounded-2xl hover:bg-brand hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-xl shadow-slate-200 hover:shadow-brand/20 flex items-center justify-center group text-sm md:text-base">
                    SEND MESSAGE <i class="fas fa-paper-plane ml-3 text-sm group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                </button>
            </form>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 md:px-6 pb-12 md:pb-20 animate__animated animate__fadeInUp animate__delay-1s">
    <div class="w-full h-[300px] md:h-[400px] rounded-[1.5rem] md:rounded-[3rem] overflow-hidden grayscale opacity-80 hover:grayscale-0 transition-all duration-1000 shadow-lg border border-teal-50 group">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14896.790403378!2d73.1022!3d21.018!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be0636952766391%3A0x6d906e001df931d!2sBardoli%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
            class="group-hover:scale-105 transition-transform duration-[5s] w-full h-full"
            style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>

<?php include 'footer.php'; ?>