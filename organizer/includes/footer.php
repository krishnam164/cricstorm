</main> <script>
        /**
         * Auto-Hide Notifications
         * Automatically fades out any alert messages after 5 seconds
         */
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-auto-hide');
            alerts.forEach(alert => {
                alert.style.transition = "opacity 0.6s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 600);
            });
        }, 5000);

        /**
         * Mobile Sidebar Toggle (Optional)
         * Helpful if you decide to add a mobile hamburger menu later
         */
        function toggleSidebar() {
            const sidebar = document.querySelector('aside');
            sidebar.classList.toggle('hidden');
        }
    </script>

    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>