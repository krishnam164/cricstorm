<?php 
include 'header.php'; 
?>

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
            <input type="text" id="tournamentSearch" placeholder="Search by tournament name..." 
                   class="w-full pl-14 pr-6 py-4 bg-primary border border-teal-50 rounded-2xl focus:outline-none focus:border-brand focus:bg-white transition-all font-medium shadow-sm focus:shadow-md">
        </div>
        
        <div class="flex bg-primary p-1.5 rounded-2xl w-full md:w-auto filter-btn-group">
            <button onclick="filterTournaments('all')" class="filter-btn active-tab flex-1 md:flex-none px-6 py-2.5 rounded-xl bg-white shadow-sm text-brand font-bold text-sm transition-all">All</button>
            <button onclick="filterTournaments('live')" class="filter-btn flex-1 md:flex-none px-6 py-2.5 rounded-xl text-slate-500 font-bold text-sm hover:text-brand transition-all">Live</button>
            <button onclick="filterTournaments('upcoming')" class="filter-btn flex-1 md:flex-none px-6 py-2.5 rounded-xl text-slate-500 font-bold text-sm hover:text-brand transition-all">Upcoming</button>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-20">
    <div id="tournamentContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php
        $query = "SELECT * FROM tournament_master ORDER BY tournament_id DESC";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $current_time = time();
                $t_date_raw = $row['tournament_date'];
                $tournament_time = strtotime($t_date_raw); 
                
                // CATEGORY LOGIC FOR FILTERING
                $category = "past";
                if ($current_time < $tournament_time) {
                    $category = "upcoming";
                    $status_label = "Upcoming"; $status_css = "bg-blue-500/90"; $dot_css = "bg-white";
                } elseif ($current_time >= $tournament_time && $current_time <= ($tournament_time + 18000)) { 
                    $category = "live";
                    $status_label = "Live Now"; $status_css = "bg-brand/90"; $dot_css = "bg-white animate-pulse";
                } else {
                    $category = "past";
                    $status_label = "Past Event"; $status_css = "bg-slate-400/90"; $dot_css = "bg-slate-200";
                }

                $db_logo = trim($row['tournament_logo'] ?? '');
                $final_logo_path = (strpos($db_logo, 'uploads/') !== false ? $db_logo : "uploads/tournaments/" . $db_logo);
        ?>
        
        <div class="tournament-card bg-surface rounded-[2rem] overflow-hidden border border-teal-50 transition-all duration-500 brand-glow group hover:-translate-y-3 animate__animated animate__fadeInUp"
             data-category="<?php echo $category; ?>" 
             data-title="<?php echo strtolower($row['tournament_name']); ?>">
            
            <div class="relative h-64 overflow-hidden bg-slate-100">
                <img src="<?php echo $final_logo_path; ?>" alt="..." class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                <div class="absolute top-5 right-5 <?php echo $status_css; ?> backdrop-blur-md text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-wider flex items-center">
                    <span class="inline-block w-2 h-2 <?php echo $dot_css; ?> rounded-full mr-2"></span>
                    <?php echo $status_label; ?>
                </div>
            </div>

            <div class="p-8">
                <h3 class="text-2xl font-bold text-slate900 group-hover:text-brand transition-colors mb-4"><?php echo $row['tournament_name']; ?></h3>
                <div class="flex gap-3">
                    <a href="player_forms.php?tid=<?php echo $row['tournament_id']; ?>" class="flex-grow flex items-center justify-center bg-slate-900 text-white font-bold py-4 rounded-2xl hover:bg-brand transition-all">PLAYER REGISTRATION</a>
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
    /* Styling for the active tab */
    .active-tab {
        background-color: white !important;
        color: #14b8a6 !important; /* Your brand teal */
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
</style>

<script>
function filterTournaments(category) {
    const cards = document.querySelectorAll('.tournament-card');
    const buttons = document.querySelectorAll('.filter-btn');

    // Update Button UI
    buttons.forEach(btn => {
        btn.classList.remove('active-tab', 'bg-white', 'shadow-sm', 'text-brand');
        btn.classList.add('text-slate-500');
    });
    event.currentTarget.classList.add('active-tab', 'bg-white', 'shadow-sm', 'text-brand');
    event.currentTarget.classList.remove('text-slate-500');

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

// Search Functionality
document.getElementById('tournamentSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.tournament-card');

    cards.forEach(card => {
        const title = card.getAttribute('data-title');
        if (title.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php include 'footer.php'; ?>