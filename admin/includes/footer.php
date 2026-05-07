</main>
</div>
<script src="../assets/library/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        const body = document.body;
        const sidebarToggle = document.getElementById("admin-sidebar-toggle");
        const backdrop = document.querySelector(".admin-sidebar-backdrop");
        const collapseButton = document.getElementById("collapse-button");

        function toggleSidebar() {
            body.classList.toggle("admin-sidebar-open");
        }

        function closeSidebar() {
            body.classList.remove("admin-sidebar-open");
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener("click", function(e) {
                e.preventDefault();
                toggleSidebar();
            });
        }

        if (backdrop) {
            backdrop.addEventListener("click", closeSidebar);
        }

        if (collapseButton) {
            collapseButton.addEventListener("click", function() {
                const isCollapsed = body.classList.toggle("folded");
                this.setAttribute("aria-expanded", !isCollapsed);
                
                // Store preference in localStorage
                localStorage.setItem("admin_menu_folded", isCollapsed ? "1" : "0");
            });

            // Load preference
            if (localStorage.getItem("admin_menu_folded") === "1") {
                body.classList.add("folded");
                collapseButton.setAttribute("aria-expanded", "false");
            }
        }

        // Handle window resize
        window.addEventListener("resize", function() {
            if (window.innerWidth > 782) {
                closeSidebar();
            }
        });
    }());
</script>
</body>
</html>
