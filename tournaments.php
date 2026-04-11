<?php 
include 'header.php'; 
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<section class="relative py-12 md:py-20 px-4 md:px-6 bg-gradient-to-b from-teal-50/50 to-primary overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] md:w-[800px] h-[300px] md:h-[400px] bg-brand/5 rounded-full blur-3xl -z-10 animate__animated animate__pulse animate__infinite animate__slow"></div>
    
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl md:text-6xl font-black text-slate-900 mb-4 md:mb-6 leading-tight animate__animated animate__fadeInDown px-2">
            Explore <span class="text-brand">Tournaments.</span>
        </h1>
        <p class="text-slate-500 text-base md:text-xl font-medium max-w-2xl mx-auto animate__animated animate__fadeIn animate__delay-1s px-4">
            Real-time auction marketplace for local and professional cricket leagues.
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 md:px-6 -mt-8 md:-mt-10 relative z-10 animate__animated animate__fadeInUp animate__delay-1s">
    <div class="bg-white p-3 md:p-6 rounded-[1.5rem] md:rounded-[2.5rem] shadow-xl border border-teal-50 flex flex-col md:flex-row gap-3 md:gap-4 items-center">
        
        <div class="relative w-full md:flex-grow group">
            <i class="fas fa-search absolute left-5 md:left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand transition-colors text-sm md:text-base"></i>
            <input type="text" id="tournamentSearch" placeholder="Search by name..." 
                   class="w-full pl-12 md:pl-14 pr-6 py-3.5 md:py-4 bg-primary border border-teal-50 rounded-xl md:rounded-2xl focus:outline-none focus:border-brand focus:bg-white transition-all font-medium shadow-sm text-sm md:text-base">
        </div>
        
        <div class="grid grid-cols-3 md:flex bg-primary p-1.5 rounded-xl md:rounded-2xl w-full md:w-auto filter-btn-group">
            <button onclick="filterTournaments('all')" class="filter-btn active-tab px-3 md:px-6 py-2.5 rounded-lg md:rounded-xl bg-white shadow-sm text-brand font-bold text-[11px] md:text-sm transition-all">All</button>
            <button onclick="filterTournaments('live')" class="filter-btn px-3 md:px-6 py-2.5 rounded-lg md:rounded-xl text-slate-500 font-bold text-[11px] md:text-sm hover:text-brand transition-all">Live</button>
            <button onclick="filterTournaments('upcoming')" class="filter-btn px-3 md:px-6 py-2.5 rounded-lg md:rounded-xl text-slate-500 font-bold text-[11px] md:text-sm hover:text-brand transition-all">Upcoming</button>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-20">
    <div id="tournamentContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-10">
        <?php
        $query = "SELECT * FROM tournament_master ORDER BY tournament_id DESC";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $current_time = time();
                $t_date_raw = $row['tournament_date'];
                $tournament_time = strtotime($t_date_raw); 
                
                if ($current_time < $tournament_time) {
                    $category = "upcoming"; $status_label = "Upcoming"; $status_css = "bg-blue-500/90"; $dot_css = "bg-white";
                } elseif ($current_time >= $tournament_time && $current_time <= ($tournament_time + 18000)) { 
                    $category = "live"; $status_label = "Live Now"; $status_css = "bg-brand/90"; $dot_css = "bg-white animate-pulse";
                } else {
                    $category = "past"; $status_label = "Past Event"; $status_css = "bg-slate-400/90"; $dot_css = "bg-slate-200";
                }

                $db_logo = trim($row['tournament_logo'] ?? '');
                $final_logo_path = (strpos($db_logo, 'uploads/') !== false ? $db_logo : "uploads/tournaments/" . $db_logo);
        ?>
        
        <div class="tournament-card bg-surface rounded-[1.5rem] md:rounded-[2rem] overflow-hidden border border-teal-50 transition-all duration-500 brand-glow group hover:-translate-y-2 animate__animated animate__fadeInUp"
             data-category="<?php echo $category; ?>" 
             data-title="<?php echo strtolower($row['tournament_name']); ?>">
            
            <div class="relative h-48 md:h-64 overflow-hidden bg-slate-100">
                <img src="<?php echo $final_logo_path; ?>" alt="..." class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                <div class="absolute top-4 right-4 <?php echo $status_css; ?> backdrop-blur-md text-white text-[9px] md:text-[10px] font-black px-3 md:px-4 py-1.5 rounded-full uppercase tracking-wider flex items-center">
                    <span class="inline-block w-2 h-2 <?php echo $dot_css; ?> rounded-full mr-2"></span>
                    <?php echo $status_label; ?>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <h3 class="text-xl md:text-2xl font-bold text-slate-900 group-hover:text-brand transition-colors mb-4 md:mb-6"><?php echo $row['tournament_name']; ?></h3>
                
                <div class="flex">
                    <?php if($category !== "past"): ?>
                        <a href="player_forms.php?tid=<?php echo $row['tournament_id']; ?>" 
                           class="w-full flex items-center justify-center bg-slate-900 text-white font-bold py-3.5 md:py-4 rounded-xl md:rounded-2xl hover:bg-brand transition-all text-xs md:text-base">
                           PLAYER REGISTRATION
                        </a>
                    <?php else: ?>
                        <div class="w-full flex items-center justify-center bg-slate-100 text-slate-400 font-bold py-3.5 md:py-4 rounded-xl md:rounded-2xl border border-dashed border-slate-200 uppercase text-[10px] md:text-xs tracking-widest">
                            Registration Closed
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php 
            }
        } 
        ?>
    </div>
</section>

<style>
    .active-tab { background-color: white !important; color: #14b8a6 !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
    /* Touch optimization */
    .filter-btn { -webkit-tap-highlight-color: transparent; }
</style>

<script>
function filterTournaments(category) {
    const cards = document.querySelectorAll('.tournament-card');
    const buttons = document.querySelectorAll('.filter-btn');
    const target = event.currentTarget;

    // Update Button UI
    buttons.forEach(btn => {
        btn.classList.remove('active-tab', 'bg-white', 'shadow-sm', 'text-brand');
        btn.classList.add('text-slate-500');
    });
    target.classList.add('active-tab', 'bg-white', 'shadow-sm', 'text-brand');
    target.classList.remove('text-slate-500');

    // Filter Cards
    cards.forEach(card => {
        const cardCat = card.getAttribute('data-category');
        if (category === 'all' || cardCat === category) {
            card.style.display = 'block';
            card.classList.add('animate__fadeInUp');
        } else {
            card.style.display = 'none';
        }
    });
}

document.getElementById('tournamentSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.tournament-card');
    cards.forEach(card => {
        const title = card.getAttribute('data-title');
        card.style.display = title.includes(searchTerm) ? 'block' : 'none';
    });
});
</script>

<?php include 'footer.php'; ?>