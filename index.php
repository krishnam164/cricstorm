<?php include 'header.php'; ?>

<header class="relative py-12 md:py-24 px-4 md:px-6 text-center bg-gradient-to-b from-teal-50/50 to-primary overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] md:w-[1000px] h-[400px] md:h-[600px] bg-brand/5 rounded-full blur-3xl -z-10 animate__animated animate__pulse animate__infinite"></div>
    
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl md:text-7xl font-extrabold text-slate900 mb-6 md:mb-8 leading-tight animate__animated animate__fadeInDown">
            The Modern Way to <br><span class="text-brand">Auction Players.</span>
        </h1>
        <p class="text-slate-500 text-lg md:text-xl mb-8 md:mb-12 max-w-2xl mx-auto font-medium animate__animated animate__fadeIn animate__delay-1s">
            CricStorm provides a seamless interface for cricket tournament organizers to manage live bidding and player stats with a professional touch.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:flex md:justify-center md:gap-6 animate__animated animate__fadeInUp animate__delay-1s">
            <div class="flex items-center justify-center space-x-3 bg-white px-6 py-3 rounded-2xl border border-teal-100 brand-glow hover:scale-105 transition-transform cursor-default">
                <i class="fas fa-check-circle text-brand"></i>
                <span class="text-sm font-bold text-slate-700">Real-time Bidding</span>
            </div>
            <div class="flex items-center justify-center space-x-3 bg-white px-6 py-3 rounded-2xl border border-teal-100 brand-glow hover:scale-105 transition-transform cursor-default">
                <i class="fas fa-check-circle text-brand"></i>
                <span class="text-sm font-bold text-slate-700">Team Management</span>
            </div>
            <div class="flex items-center justify-center space-x-3 bg-white px-6 py-3 rounded-2xl border border-teal-100 brand-glow hover:scale-105 transition-transform cursor-default">
                <i class="fas fa-check-circle text-brand"></i>
                <span class="text-sm font-bold text-slate-700">Auto-Calculation</span>
            </div>
        </div>
    </div>
</header>

<section class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-10">
    <div class="relative rounded-[2rem] md:rounded-[3rem] overflow-hidden bg-slate-900 h-[450px] md:h-[600px] flex items-center shadow-2xl group animate__animated animate__zoomIn">
        <img src="https://images.unsplash.com/photo-1531415074968-036ba1b565da?auto=format&fit=crop&q=80&w=2070" 
            alt="Cricket Stadium" 
            class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:scale-110 transition-transform duration-[10s]">

        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/60 to-transparent"></div>

        <div class="relative z-10 px-6 md:px-20 max-w-2xl">
            <div class="inline-flex items-center space-x-2 bg-brand/20 border border-brand/30 px-3 py-1 rounded-full mb-6">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-brand"></span>
                </span>
                <span class="text-brand font-bold tracking-widest uppercase text-[10px]">Professional Grade</span>
            </div>
            
            <h2 class="text-3xl md:text-6xl font-black text-white mb-6 leading-tight">
                Real-Time <br><span class="text-brand">Auction Control.</span>
            </h2>
            
            <div class="flex flex-row gap-3 md:gap-4 mt-8">
                <div class="flex items-center space-x-2 md:space-x-3 bg-white/10 backdrop-blur-md border border-white/10 px-4 py-3 rounded-2xl hover:bg-white/20 transition-all cursor-default">
                    <i class="fas fa-gavel text-brand"></i>
                    <span class="text-white text-xs md:text-sm font-bold tracking-tight">Hammer Logic</span>
                </div>
                <div class="flex items-center space-x-2 md:space-x-3 bg-white/10 backdrop-blur-md border border-white/10 px-4 py-3 rounded-2xl hover:bg-white/20 transition-all cursor-default">
                    <i class="fas fa-bolt text-brand"></i>
                    <span class="text-white text-xs md:text-sm font-bold tracking-tight">Instant Sync</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="max-w-4xl mx-auto px-6 py-12 md:py-20 text-center animate-on-scroll">
    <h2 class="flex items-center justify-center gap-3 text-2xl md:text-4xl font-black text-slate-900 mb-8 md:mb-10">
        <span class="text-xl md:text-2xl animate-bounce">🏏</span> What is CricStorm? <span class="text-xl md:text-2xl animate-bounce">🏏</span>
    </h2>
    <div class="space-y-6 text-slate-600 leading-relaxed text-base md:text-lg px-2">
        <p class="hover:text-slate-900 transition-colors">
            CricStorm is an innovative online player auction platform designed specifically for cricket tournament organizers. It simplifies the entire player auction process, eliminating the need for cumbersome Excel sheets and manual tasks.
        </p>
        <p class="hover:text-slate-900 transition-colors">
            Our platform also enhances the experience for your sponsors by allowing you to showcase their advertisements directly on the auction screen, making your event more engaging and professional.
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-12 md:py-20 bg-primary">
    <div class="text-center mb-12 md:mb-16">
        <h2 class="flex items-center justify-center gap-3 text-2xl md:text-4xl font-black text-slate-900">
            <span class="text-xl">🏏</span> Advanced Features <span class="text-xl">🏏</span>
        </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
        <?php 
        $features = [
            ['fab fa-youtube', 'Live Streaming'],
            ['fas fa-users', 'Team Owner View'],
            ['fas fa-gavel', 'Remotely Bid'],
            ['fas fa-headset', 'Customer Support']
        ];
        foreach($features as $f): ?>
        <div class="bg-white p-8 md:p-10 rounded-2xl shadow-lg border border-slate-50 flex flex-col items-center text-center group transition-all hover:-translate-y-2 md:hover:-translate-y-4 duration-300">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-brand/10 text-brand rounded-2xl flex items-center justify-center text-3xl md:text-4xl mb-4 md:mb-6 group-hover:bg-brand group-hover:text-white group-hover:rotate-12 transition-all">
                <i class="<?php echo $f[0]; ?>"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-lg"><?php echo $f[1]; ?></h3>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="working" class="max-w-7xl mx-auto px-4 md:px-6 py-16 md:py-24 bg-white rounded-[2rem] md:rounded-[3rem] my-6 md:my-10 shadow-sm border border-teal-50">
    <div class="text-center mb-12 md:mb-20">
        <h2 class="text-3xl md:text-5xl font-black text-slate-900 leading-tight">
            How To <span class="text-brand">Create Auction</span>
        </h2>
    </div>

    <div class="flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-16">
        <div class="lg:w-1/2 relative group px-10">
            <div class="absolute inset-0 bg-brand/10 rounded-full blur-3xl opacity-50 transition-opacity"></div>
            <img src="images/icon.png" 
                 alt="App Mockup" 
                 class="relative z-10 w-full max-w-[280px] md:max-w-md mx-auto drop-shadow-2xl transform -rotate-2 group-hover:rotate-0 group-hover:scale-105 transition-all duration-700 animate-bounce-slow">
        </div>

        <div class="lg:w-1/2 space-y-8 md:space-y-10 w-full">
            <?php 
            $steps = [
                ['01', 'SIGN UP', 'Sign in to the platform with OTP verification using your mobile number.', 'from-pink-500 to-rose-600'],
                ['02', 'CREATE AUCTION', 'Provide basic details and upload your auction logo.', 'from-purple-500 to-indigo-600'],
                ['03', 'ADD TEAMS', 'Add teams and logos one by one via the management dashboard.', 'from-blue-500 to-cyan-600'],
                ['04', 'ADD PLAYERS', 'Share registration links or add players manually to the list.', 'from-teal-500 to-emerald-600'],
                ['05', 'AUCTION DASHBOARD', 'Manage your live auction and monitor bids in real-time.', 'from-orange-500 to-yellow-600'],
                ['06', 'SUMMARY SCREEN', 'View final team rosters and auction statistics.', 'from-green-500 to-teal-600'],
            ];
            foreach($steps as $step):
            ?>
            <div class="flex items-start gap-4 md:gap-6 group">
                <div class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br <?php echo $step[3]; ?> rounded-full flex items-center justify-center text-white text-sm md:text-base font-black shadow-lg group-hover:scale-110 transition-all">
                    <?php echo $step[0]; ?>
                </div>
                <div>
                    <h4 class="text-lg md:text-xl font-bold text-slate-900 group-hover:text-brand transition-colors"><?php echo $step[1]; ?></h4>
                    <p class="text-slate-500 text-sm mt-1 leading-relaxed"><?php echo $step[2]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-16">
    <div class="flex items-center justify-between mb-8 md:mb-12">
        <h2 class="text-2xl md:text-3xl font-extrabold text-slate900 border-l-8 border-brand pl-4 md:pl-5">Active Tournaments</h2>
        <span class="hidden sm:block text-[10px] md:text-xs font-bold tracking-widest text-slate-400 uppercase animate-pulse">Live Marketplace</span>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-10">
        <?php
        // ... (Database logic remains exactly as you provided) ...
        $query = "SELECT * FROM tournament_master ORDER BY tournament_id DESC LIMIT 6";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $current_time = time();
                $t_date_raw = $row['tournament_date'] ?? $row['created_at'] ?? 'now';
                $tournament_time = strtotime($t_date_raw); 
                
                if ($current_time < $tournament_time) {
                    $status_label = "Upcoming"; $status_css = "bg-blue-500/90"; $dot_css = "bg-white";
                } elseif ($current_time >= $tournament_time && $current_time <= ($tournament_time + 18000)) { 
                    $status_label = "Live Now"; $status_css = "bg-brand/90"; $dot_css = "bg-white animate-pulse";
                } else {
                    $status_label = "Past Event"; $status_css = "bg-slate-400/90"; $dot_css = "bg-slate-200";
                }

                $db_logo = trim($row['tournament_logo'] ?? '');
                $final_logo_path = (strpos($db_logo, 'uploads/') !== false ? $db_logo : "uploads/tournaments/" . $db_logo);
        ?>
        
        <div class="bg-surface rounded-[1.5rem] md:rounded-[2rem] overflow-hidden border border-teal-50 hover:border-brand/30 transition-all duration-500 brand-glow group hover:-translate-y-2">
            <div class="relative h-48 md:h-60 overflow-hidden bg-slate-100">
                <img src="<?php echo $final_logo_path; ?>" 
                     alt="<?php echo $row['tournament_name']; ?>"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                     onerror="this.onerror=null;this.src='images/default_tournament.png';">
                
                <div class="absolute top-4 right-4 <?php echo $status_css; ?> backdrop-blur-md text-white text-[9px] md:text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-wider shadow-lg flex items-center">
                    <span class="inline-block w-2 h-2 <?php echo $dot_css; ?> rounded-full mr-2"></span>
                    <?php echo $status_label; ?>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <h3 class="text-xl md:text-2xl font-bold text-slate900 group-hover:text-brand transition-colors mb-4 leading-tight">
                    <?php echo $row['tournament_name']; ?>
                </h3>
                
                <div class="space-y-3 mb-6 md:mb-8">
                    <div class="flex items-center text-slate-500 text-sm font-medium">
                        <i class="far fa-calendar-alt w-6 text-brand"></i>
                        <span>Date: <?php echo date('d M, Y', $tournament_time); ?></span>
                    </div>
                    <div class="flex items-center text-slate-500 text-sm font-medium">
                        <i class="far fa-clock w-6 text-brand"></i>
                        <span>Time: <?php echo date('h:i A', $tournament_time); ?></span>
                    </div>
                </div>

                <div class="flex">
                    <?php if($status_label !== "Past Event"): ?>
                        <a href="player_forms.php?tid=<?php echo $row['tournament_id']; ?>" 
                           class="w-full flex items-center justify-center bg-slate-900 text-white font-bold py-3 md:py-4 rounded-xl md:rounded-2xl hover:bg-brand transition-all shadow-lg">
                           PLAYER REGISTRATION
                        </a>
                    <?php else: ?>
                        <button class="w-full flex items-center justify-center bg-slate-100 text-slate-400 font-bold py-3 md:py-4 rounded-xl md:rounded-2xl cursor-not-allowed">
                            AUCTION ENDED
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php 
            }
        } else {
            echo "<p class='text-slate-400 font-bold text-lg text-center col-span-full'>No tournaments are currently live.</p>";
        }
        ?>
    </div>
</section>

<style>
    @keyframes bounce-slow { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-15px) rotate(2deg); } }
    .animate-bounce-slow { animation: bounce-slow 6s ease-in-out infinite; }
    .brand-glow:hover {
        box-shadow: 0 20px 25px -5px rgba(20, 184, 166, 0.1), 0 10px 10px -5px rgba(20, 184, 166, 0.04);
    }
    /* Smooth transition for mobile button taps */
    a, button { -webkit-tap-highlight-color: transparent; }
</style>

<?php include 'footer.php'; ?>