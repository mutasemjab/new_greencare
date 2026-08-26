document.addEventListener('DOMContentLoaded', function () {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggler  = document.getElementById('sidebarToggler');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const body     = document.body;

    // Mobile: open/close sidebar via hamburger
    if (toggler) {
        toggler.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });
    }

    // Detect RTL
    const isRtl = document.documentElement.dir === 'rtl';

    // Desktop: collapse sidebar to icon-only mode
    if (collapseBtn) {
        collapseBtn.addEventListener('click', () => {
            body.classList.toggle('sidebar-collapsed');
            const isCollapsed = body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            // In RTL the arrow direction is already flipped by CSS; use same icon names
            collapseBtn.querySelector('i').className =
                isCollapsed ? 'bi bi-arrow-bar-right' : 'bi bi-arrow-bar-left';
        });
    }

    // Restore collapse state on load
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        body.classList.add('sidebar-collapsed');
        if (collapseBtn) {
            collapseBtn.querySelector('i').className = 'bi bi-arrow-bar-right';
        }
    }

    // Submenu accordion toggle — active/open state for the current page is
    // already rendered server-side (Blade checks request()->routeIs(...)),
    // this just lets the user manually expand/collapse a section.
    document.querySelectorAll('.has-submenu > .submenu-toggle').forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            this.closest('.has-submenu').classList.toggle('open');
        });
    });

    // Remember the sidebar's scroll position across full-page navigations.
    // Without this, clicking a link far down a long, fully-expanded sidebar
    // reloads the page and resets the scroll to the top, losing the section
    // the user was just in.
    const sidebarNav = sidebar ? sidebar.querySelector('.sidebar-nav') : null;
    if (sidebarNav) {
        const savedScroll = sessionStorage.getItem('sidebarScrollTop');
        if (savedScroll !== null) {
            sidebarNav.scrollTop = parseInt(savedScroll, 10);
        }
        sidebarNav.querySelectorAll('a[href]:not([href="#"])').forEach(link => {
            link.addEventListener('click', () => {
                sessionStorage.setItem('sidebarScrollTop', sidebarNav.scrollTop);
            });
        });
    }

    // Close sidebar on mobile when a regular nav link is clicked
    document.querySelectorAll('.sidebar-nav .nav-link:not(.submenu-toggle)').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            }
        });
    });
});
