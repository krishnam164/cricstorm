<?php include 'header.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<section class="relative py-20 px-6 bg-gradient-to-b from-teal-50/50 to-primary overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-brand/5 rounded-full blur-3xl -z-10 animate__animated animate__pulse animate__infinite animate__slow"></div>
    
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-5xl md:text-6xl font-black text-slate900 mb-6 leading-tight animate__animated animate__fadeInDown">
            Get in <span class="text-brand">Touch.</span>
        </h1>
        <p class="text-slate-500 text-lg md:text-xl font-medium max-w-2xl mx-auto animate__animated animate__fadeIn animate__delay-1s">
            Have questions about setting up your auction? Our team is here to help you hit a six.
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
        
        <div class="space-y-12 animate__animated animate__fadeInLeft">
            <div>
                <h2 class="text-3xl font-black text-slate900 mb-6 border-l-8 border-brand pl-6">Contact Details</h2>
                <p class="text-slate-500 text-lg leading-relaxed">
                    Whether you're a local club or a professional league, we provide the support you need to run a seamless auction.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8">
                <div class="flex items-center space-x-6 p-6 bg-white rounded-3xl border border-teal-50 brand-glow hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-brand text-white rounded-2xl flex items-center justify-center text-xl group-hover:rotate-12 transition-transform">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Call Us</h4>
                        <p class="text-lg font-bold text-slate-900">+91 97376 31021</p>
                    </div>
                </div>

                <div class="flex items-center space-x-6 p-6 bg-white rounded-3xl border border-teal-50 brand-glow hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-brand text-white rounded-2xl flex items-center justify-center text-xl group-hover:rotate-12 transition-transform">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Email Support</h4>
                        <p class="text-lg font-bold text-slate-900">contact@cricstrome.com</p>
                    </div>
                </div>

                <div class="flex items-center space-x-6 p-6 bg-white rounded-3xl border border-teal-50 brand-glow hover:-translate-y-2 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-brand text-white rounded-2xl flex items-center justify-center text-xl group-hover:rotate-12 transition-transform">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Headquarters</h4>
                        <p class="text-lg font-bold text-slate-900">Bardoli, Gujarat, India</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 md:p-12 rounded-[3rem] shadow-xl border border-teal-50 animate__animated animate__fadeInRight">
            <h3 class="text-2xl font-black text-slate-900 mb-8">Send a Message</h3>
            
            <form action="process_contact.php" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase ml-2">Full Name</label>
                        <input type="text" name="name" placeholder="John Doe" 
                               class="w-full px-6 py-4 bg-primary border border-teal-50 rounded-2xl focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/5 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase ml-2">Phone Number</label>
                        <input type="tel" name="phone" placeholder="+91 00000 00000" 
                               class="w-full px-6 py-4 bg-primary border border-teal-50 rounded-2xl focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/5 transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase ml-2">Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" 
                           class="w-full px-6 py-4 bg-primary border border-teal-50 rounded-2xl focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/5 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase ml-2">Your Message</label>
                    <textarea name="message" rows="4" placeholder="Tell us about your tournament..." 
                              class="w-full px-6 py-4 bg-primary border border-teal-50 rounded-2xl focus:outline-none focus:border-brand focus:ring-4 focus:ring-brand/5 transition-all resize-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-slate900 text-white font-black py-5 rounded-2xl hover:bg-brand hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-xl shadow-slate-200 hover:shadow-brand/20 flex items-center justify-center group">
                    SEND MESSAGE <i class="fas fa-paper-plane ml-3 text-sm group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                </button>
            </form>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 pb-20 animate__animated animate__fadeInUp animate__delay-1s">
    <div class="w-full h-[400px] rounded-[3rem] overflow-hidden grayscale opacity-80 hover:grayscale-0 transition-all duration-1000 shadow-lg border border-teal-50 group">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59556.36015569477!2d73.07661148866767!3d21.101734288824147!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04264639a068d%3A0x1d43615951a84f50!2sBardoli%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1709923200000!5m2!1sen!2sin" 
            class="group-hover:scale-105 transition-transform duration-[5s]"
            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>

<?php include 'footer.php'; ?>