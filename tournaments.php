<?php 
include 'header.php'; 
// Database connection is inherited from header.php -> config.php
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<section class="relative py-20 px-6 bg-gradient-to-b from-teal-50/50 to-primary overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-brand/5 rounded-full blur-3xl -z-10 animate__animated animate__pulse animate__infinite animate__slow"></div>
    
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-5xl md:text-6xl font-black text-slate-900 mb-6 leading-tight animate__animated animate__fadeInDown">
            Explore <span class="text-brand">Tournaments.</span>
        </h1>
        <p class="text-slate-500 text-lg md:text-xl font-medium max-w-2xl mx-auto animate__animated animate__fadeIn animate__delay-1s">
            Real-time auction marketplace for local and professional cricket leagues.
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 -mt-10 relative z-10 animate__animated animate__fadeInUp animate__delay-1s">
    <div class="bg-white p-4 md:p-6 rounded-[2.5rem] shadow-xl border border-teal-50 flex flex-col md:flex-row gap-4 items-center">
        <div class="relative w-full md:flex-grow group">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand transition-colors"></i>
            <input type="text" placeholder="Search by tournament name..." 
                   class="w-full pl-14 pr-6 py-4 bg-primary border border-teal-50 rounded-2xl focus:outline-none focus:border-brand focus:bg-white transition-all font-medium shadow-sm focus:shadow-md">
        </div>
        
        <div class="flex bg-primary p-1.5 rounded-2xl w-full md:w-auto">
            <button class="flex-1 md:flex-none px-6 py-2.5 rounded-xl bg-white shadow-sm text-brand font-bold text-sm hover:scale-105 transition-transform">All</button>
            <button class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-slate-500 font-bold text-sm hover:text-brand hover:bg-white/50 transition-all">Live</button>
            <button class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-slate-500 font-bold text-sm hover:text-brand hover:bg-white/50 transition-all">Upcoming</button>
        </div>

        <a href="login.php" class="w-full md:w-auto bg-brand text-white px-8 py-4 rounded-2xl font-black hover:bg-brandDark hover:scale-105 active:scale-95 transition-all shadow-lg shadow-brand/20 text-center">
            + CREATE NEW
        </a>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php
        $query = "SELECT * FROM tournament_master ORDER BY tournament_id DESC";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0) {
            $delay_counter = 0;
            while($row = mysqli_fetch_assoc($result)) {
                $delay_class = $delay_counter < 6 ? "animate__delay-{$delay_counter}s" : "";
                
                // --- DYNAMIC TIME LOGIC ---
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
        
        <div class="bg-surface rounded-[2rem] overflow-hidden border border-teal-50 hover:border-brand/30 transition-all duration-500 brand-glow group hover:-translate-y-3 animate__animated animate__fadeInUp">
            <div class="relative h-64 overflow-hidden bg-slate-100">
                <img src="<?php echo $final_logo_path; ?>" 
                     alt="<?php echo $row['tournament_name']; ?>"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000"
                     onerror="this.onerror=null;this.src='images/default_tournament.png';">
                
                <div class="absolute top-5 right-5 <?php echo $status_css; ?> backdrop-blur-md text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-wider shadow-lg flex items-center">
                    <span class="inline-block w-2 h-2 <?php echo $dot_css; ?> rounded-full mr-2"></span>
                    <?php echo $status_label; ?>
                </div>
            </div>

            <div class="p-8">
                <h3 class="text-2xl font-bold text-slate900 group-hover:text-brand transition-colors mb-4 leading-tight">
                    <?php echo $row['tournament_name']; ?>
                </h3>
                
                <div class="space-y-3 mb-8">
                    <div class="flex items-center text-slate-500 text-sm font-medium">
                        <i class="far fa-calendar-alt w-6 text-brand group-hover:rotate-12 transition-transform"></i>
                        <span>Date: <?php echo date('d M, Y', $tournament_time); ?></span>
                    </div>
                    <div class="flex items-center text-slate-500 text-sm font-medium">
                        <i class="far fa-clock w-6 text-brand group-hover:rotate-12 transition-transform"></i>
                        <span>Time: <?php echo date('h:i A', $tournament_time); ?></span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <?php if($status_label !== "Past Event"): ?>
                        <a href="live_auction.php?tid=<?php echo $row['tournament_id']; ?>" 
                           class="flex-grow flex items-center justify-center bg-slate900 text-white font-bold py-4 rounded-2xl hover:bg-brand hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-xl shadow-slate-200">
                            ENTER AUCTION
                        </a>
                    <?php else: ?>
                        <button class="flex-grow flex items-center justify-center bg-slate-100 text-slate-400 font-bold py-4 rounded-2xl cursor-not-allowed">
                            AUCTION ENDED
                        </button>
                    <?php endif; ?>
                    
                    <button class="w-14 h-14 flex items-center justify-center border border-teal-100 rounded-2xl text-slate-400 hover:text-brand hover:border-brand hover:bg-brand/5 hover:rotate-12 transition-all">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <?php 
            $delay_counter++;
            }
        } else {
            echo "
            <div class='col-span-full py-32 text-center bg-white rounded-[3rem] border-2 border-dashed border-teal-100 animate__animated animate__zoomIn'>
                <div class='w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce'>
                    <i class='fas fa-search text-3xl text-brand'></i>
                </div>
                <h3 class='text-xl font-bold text-slate-900 mb-2'>No Tournaments Found</h3>
                <p class='text-slate-400 font-medium'>Be the first to create a professional cricket auction!</p>
            </div>";
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>