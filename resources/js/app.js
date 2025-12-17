import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;
// Initialize Alpine
Alpine.start();
import Swal from "sweetalert2";
window.Swal = Swal;
import $ from "jquery";
window.$ = window.jQuery = $;

// Use dynamic import to ensure jQuery is available globally before Select2 loads
import("select2").then((select2) => {
    // If select2 exports a factory function (CommonJS), initialize it
    if (typeof select2.default === "function") {
        select2.default(window, $);
    }
    import("select2/dist/css/select2.min.css");

    $(document).ready(function () {
        $(".select2").select2();
    });
});

// UI helpers
function setActiveNav() {
    try {
        let customUrls = [];
        document
            .querySelectorAll(".active-nav-custom-url")
            .forEach(function (element) {
                customUrls.push(element.getAttribute("url"));
            });
        const path = window.location.pathname.replace(/\/$/, "") || "/";
        document.querySelectorAll(".app-nav-item").forEach((el) => {
            const isHome = el.dataset.route === "home";
            const href = el.getAttribute("href") || "";
            // Consider active if exact route match or if data-route matches 'settings' for settings page
            const active =
                (isHome && (path === "/" || path === "/home")) ||
                (href &&
                    href !== "#" &&
                    (href === path ||
                        href.endsWith(path) ||
                        customUrls.includes(href)));

            el.classList.toggle("nav-active", active);
            if (!active) {
                el.classList.remove("nav-active");
            }
        });
    } catch (e) {}
}

function initTheme() {
    const key = "darkMode";
    const prefersDark =
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches;
    const saved = localStorage.getItem(key);
    const isDark = saved === "true" || (saved === null && prefersDark);
    document.documentElement.classList.toggle("dark", isDark);
}

function toggleDarkMode() {
    const isDark = document.documentElement.classList.toggle("dark");
    localStorage.setItem("darkMode", isDark);
}

function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    if (!sidebar || !overlay) {
        return;
    }
    const isOpen = sidebar.style.transform === "translateX(0px)";
    if (isOpen) {
        sidebar.style.transform = "translateX(-100%)";
        overlay.style.display = "none";
    } else {
        sidebar.style.transform = "translateX(0px)";
        overlay.style.display = "block";
    }
}

// Expose for Blade inline onclick handlers
window.__toggleSidebar = toggleSidebar;
window.__toggleDarkMode = toggleDarkMode;

window.addEventListener("DOMContentLoaded", () => {
    initTheme();
    setActiveNav();
});

window.addEventListener("resize", () => {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    if (!sidebar || !overlay) {
        return;
    }
    if (window.innerWidth >= 1024) {
        sidebar.style.transform = "translateX(0px)";
        overlay.style.display = "none";
    } else {
        sidebar.style.transform = "translateX(-100%)";
    }
});
