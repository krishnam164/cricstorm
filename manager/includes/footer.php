</main> 

<script>
    /**
     * Mobile Sidebar & Overlay Toggle
     */
    function toggleSidebar() {
        const sidebar = document.getElementById('managerSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar && overlay) {
            sidebar.classList.toggle('-translate-x-0');
            overlay.classList.toggle('hidden');
            
            // Prevent background scrolling
            if (sidebar.classList.contains('-translate-x-0')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        } else {
            console.error("Sidebar elements not found in the DOM. Check your IDs!");
        }
    }

    /**
     * Modal Control Logic
     */
    function closeModal() {
        const modal = document.getElementById('playerModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scroll
    }

    // Close Modal/Sidebar on Escape Key
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            closeModal();
            const sidebar = document.getElementById('main-sidebar');
            if (sidebar && sidebar.classList.contains('active')) {
                toggleSidebar();
            }
        }
    });

    /**
     * Auto-Hide Notifications
     */
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-auto-hide');
        alerts.forEach(alert => {
            alert.style.transition = "all 0.6s cubic-bezier(0.4, 0, 0.2, 1)";
            alert.style.opacity = "0";
            alert.style.transform = "translateX(20px)";
            setTimeout(() => alert.remove(), 600);
        });
    }, 5000);
</script>

<script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>

<div id="playerModal" class="fixed inset-0 z-[150] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" onclick="if(event.target === this) closeModal()">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20 animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <h3 id="modalTitle" class="text-sm font-black text-slate-800 uppercase italic tracking-tighter">Team Squad</h3>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all active:scale-90">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <div id="modalContent" class="p-6 max-h-[60vh] overflow-y-auto">
            <div class="flex justify-center p-10">
                <i class="fas fa-circle-notch fa-spin text-teal-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

</body>
</html>