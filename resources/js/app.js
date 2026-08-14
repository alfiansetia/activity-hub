// Sidebar toggle — works on both desktop and mobile
window.toggleSidebar = function () {
    const isMobile = window.innerWidth < 992;

    if (isMobile) {
        // Mobile: slide sidebar in/out with backdrop
        document.body.classList.toggle("sidebar-open");
        document.querySelector(".sidebar")?.classList.toggle("show");
        document.querySelector(".sidebar-backdrop")?.classList.toggle("show");
    } else {
        // Desktop: collapse/expand sidebar
        document.body.classList.toggle("sidebar-collapsed");
    }
};

// Close sidebar on backdrop click (mobile)
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("sidebar-backdrop")) {
        toggleSidebar();
    }
});
