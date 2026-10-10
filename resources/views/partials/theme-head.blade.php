{{-- Synchronous Anti-FOUC Script: Runs before DOM painting to prevent theme flicker --}}
<script>
    (function() {
        var match = document.cookie.match(/(?:^|; )theme=([^;]*)/);
        var theme = match ? decodeURIComponent(match[1]) : 'dark';
        if (theme === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        }
    })();
</script>

<style>
    /* ==========================================================================
       Vercel Light Theme System Overrides (Active when html has .light / not .dark)
       ========================================================================== */
    html.light body,
    html:not(.dark) body {
        background-color: #fafafa !important;
        color: #111827 !important;
        selection-background-color: #e5e7eb;
    }

    /* Scrollbars in Light Mode */
    html.light ::-webkit-scrollbar-track,
    html:not(.dark) ::-webkit-scrollbar-track {
        background: transparent !important;
    }
    html.light ::-webkit-scrollbar-thumb,
    html:not(.dark) ::-webkit-scrollbar-thumb {
        background: #d4d4d8 !important;
    }
    html.light ::-webkit-scrollbar-thumb:hover,
    html:not(.dark) ::-webkit-scrollbar-thumb:hover {
        background: #a1a1aa !important;
    }
    html.light *,
    html:not(.dark) * {
        scrollbar-color: #d4d4d8 transparent !important;
    }

    /* Surfaces & Containers */
    html.light .bg-\[\#0a0a0a\],
    html:not(.dark) .bg-\[\#0a0a0a\] {
        background-color: #ffffff !important;
    }
    html.light .bg-\[\#0a0a0a\]\/90,
    html:not(.dark) .bg-\[\#0a0a0a\]\/90 {
        background-color: rgba(255, 255, 255, 0.92) !important;
    }
    html.light .bg-\[\#111111\],
    html:not(.dark) .bg-\[\#111111\] {
        background-color: #ffffff !important;
    }
    html.light .bg-\[\#161616\],
    html:not(.dark) .bg-\[\#161616\] {
        background-color: #f4f4f5 !important;
    }
    html.light .bg-\[\#171717\],
    html:not(.dark) .bg-\[\#171717\] {
        background-color: #f8f9fa !important;
    }
    html.light .bg-\[\#1a1a1a\],
    html:not(.dark) .bg-\[\#1a1a1a\] {
        background-color: #f1f3f5 !important;
    }
    html.light .bg-\[\#1f1f1f\],
    html:not(.dark) .bg-\[\#1f1f1f\] {
        background-color: #e9ecef !important;
    }
    html.light .bg-\[\#222222\],
    html:not(.dark) .bg-\[\#222222\] {
        background-color: #e5e7eb !important;
    }

    /* Hover States for Backgrounds */
    html.light .hover\:bg-\[\#161616\]:hover,
    html:not(.dark) .hover\:bg-\[\#161616\]:hover {
        background-color: #f4f4f5 !important;
    }
    html.light .hover\:bg-\[\#1f1f1f\]:hover,
    html:not(.dark) .hover\:bg-\[\#1f1f1f\]:hover {
        background-color: #f1f3f5 !important;
    }
    html.light .hover\:bg-\[\#222222\]:hover,
    html:not(.dark) .hover\:bg-\[\#222222\]:hover {
        background-color: #e9ecef !important;
    }
    html.light .hover\:bg-\[\#333333\]:hover,
    html:not(.dark) .hover\:bg-\[\#333333\]:hover {
        background-color: #e2e8f0 !important;
    }

    /* Borders */
    html.light .border-\[\#222222\],
    html:not(.dark) .border-\[\#222222\] {
        border-color: #eaeaea !important;
    }
    html.light .border-\[\#333333\],
    html:not(.dark) .border-\[\#333333\] {
        border-color: #e5e5e5 !important;
    }
    html.light .border-\[\#444444\],
    html:not(.dark) .border-\[\#444444\] {
        border-color: #d4d4d4 !important;
    }
    html.light .border-\[\#555555\],
    html:not(.dark) .border-\[\#555555\] {
        border-color: #a1a1aa !important;
    }
    html.light .hover\:border-\[\#555555\]:hover,
    html:not(.dark) .hover\:border-\[\#555555\]:hover {
        border-color: #71717a !important;
    }
    html.light .hover\:border-white:hover,
    html:not(.dark) .hover\:border-white:hover {
        border-color: #111827 !important;
    }
    html.light .focus\:border-white:focus,
    html:not(.dark) .focus\:border-white:focus {
        border-color: #111827 !important;
    }

    /* Text Colors */
    html.light .text-white,
    html:not(.dark) .text-white {
        color: #111827 !important;
    }
    html.light .text-\[\#ededed\],
    html:not(.dark) .text-\[\#ededed\] {
        color: #18181b !important;
    }
    html.light .text-\[\#a1a1a1\],
    html:not(.dark) .text-\[\#a1a1a1\] {
        color: #52525b !important;
    }
    html.light .text-\[\#888888\],
    html:not(.dark) .text-\[\#888888\] {
        color: #71717a !important;
    }
    html.light .text-\[\#666666\],
    html:not(.dark) .text-\[\#666666\] {
        color: #888888 !important;
    }
    html.light .hover\:text-white:hover,
    html:not(.dark) .hover\:text-white:hover {
        color: #000000 !important;
    }

    /* Primary CTA Buttons & Active Pills (Solid Dark in Light Mode) */
    html.light .bg-white,
    html:not(.dark) .bg-white {
        background-color: #111827 !important;
        color: #ffffff !important;
    }
    html.light .bg-white *,
    html:not(.dark) .bg-white * {
        color: #ffffff !important;
    }
    html.light .bg-white:hover,
    html:not(.dark) .bg-white:hover,
    html.light .hover\:bg-\[\#e6e6e6\]:hover,
    html:not(.dark) .hover\:bg-\[\#e6e6e6\]:hover {
        background-color: #27272a !important;
        color: #ffffff !important;
    }
    html.light .bg-white:hover *,
    html:not(.dark) .bg-white:hover * {
        color: #ffffff !important;
    }

    /* Keep White Text for Colorful or Dark Solid Elements */
    html.light .keep-white,
    html:not(.dark) .keep-white,
    html.light .bg-\[\#0070f3\],
    html:not(.dark) .bg-\[\#0070f3\],
    html.light .bg-\[\#0070f3\] *,
    html:not(.dark) .bg-\[\#0070f3\] *,
    html.light .bg-\[\#ff453a\],
    html:not(.dark) .bg-\[\#ff453a\],
    html.light .bg-\[\#ff453a\] *,
    html:not(.dark) .bg-\[\#ff453a\] *,
    html.light .bg-\[\#c50000\],
    html:not(.dark) .bg-\[\#c50000\],
    html.light .bg-\[\#c50000\] *,
    html:not(.dark) .bg-\[\#c50000\] *,
    html.light .bg-\[\#ee0000\],
    html:not(.dark) .bg-\[\#ee0000\],
    html.light .bg-\[\#ee0000\] *,
    html:not(.dark) .bg-\[\#ee0000\] * {
        color: #ffffff !important;
    }

    /* Form Inputs & Selects */
    html.light input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="submit"]):not([type="button"]),
    html.light textarea,
    html.light select,
    html:not(.dark) input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="submit"]):not([type="button"]),
    html:not(.dark) textarea,
    html:not(.dark) select {
        background-color: #ffffff !important;
        border-color: #e5e5e5 !important;
        color: #111827 !important;
    }
    html.light input::placeholder,
    html.light textarea::placeholder,
    html:not(.dark) input::placeholder,
    html:not(.dark) textarea::placeholder {
        color: #9ca3af !important;
    }
    html.light input:focus,
    html.light textarea:focus,
    html.light select:focus,
    html:not(.dark) input:focus,
    html:not(.dark) textarea:focus,
    html:not(.dark) select:focus {
        border-color: #111827 !important;
        outline: none !important;
    }

    /* Range Slider */
    html.light input[type="range"],
    html:not(.dark) input[type="range"] {
        background: #e5e7eb !important;
    }
    html.light input[type="range"]::-webkit-slider-thumb,
    html:not(.dark) input[type="range"]::-webkit-slider-thumb {
        background: #111827 !important;
        border: 2px solid #ffffff !important;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.2) !important;
    }

    /* ==========================================================================
       Photo & Card Overlay Badges in Light Mode (High Contrast & Clear Readability)
       ========================================================================== */
    html.light .bg-black\/80,
    html:not(.dark) .bg-black\/80 {
        background-color: rgba(255, 255, 255, 0.94) !important;
        border-color: rgba(0, 0, 0, 0.12) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
    }
    html.light .bg-black\/80.text-white,
    html.light .bg-black\/80 .text-white,
    html:not(.dark) .bg-black\/80.text-white,
    html:not(.dark) .bg-black\/80 .text-white {
        color: #0f172a !important;
    }
    html.light .bg-black\/80.text-\[\#a1a1a1\],
    html.light .bg-black\/80 .text-\[\#a1a1a1\],
    html:not(.dark) .bg-black\/80.text-\[\#a1a1a1\],
    html:not(.dark) .bg-black\/80 .text-\[\#a1a1a1\] {
        color: #334155 !important;
    }
    html.light .bg-black\/80.text-\[\#888888\],
    html.light .bg-black\/80 .text-\[\#888888\],
    html:not(.dark) .bg-black\/80.text-\[\#888888\],
    html:not(.dark) .bg-black\/80 .text-\[\#888888\] {
        color: #475569 !important;
    }

    /* ==========================================================================
       Status Badges & Chips - Light Mode Clean Pastels
       ========================================================================== */
    /* Green / Success */
    html.light .bg-\[\#002b11\],
    html.light .bg-\[\#002b11\]\/90,
    html.light .bg-\[\#002b11\]\/80,
    html:not(.dark) .bg-\[\#002b11\],
    html:not(.dark) .bg-\[\#002b11\]\/90,
    html:not(.dark) .bg-\[\#002b11\]\/80 {
        background-color: #ecfdf5 !important;
        border-color: #a7f3d0 !important;
        color: #059669 !important;
    }
    html.light .bg-\[\#002b11\] *,
    html.light .bg-\[\#002b11\]\/90 *,
    html.light .bg-\[\#002b11\]\/80 *,
    html:not(.dark) .bg-\[\#002b11\] *,
    html:not(.dark) .bg-\[\#002b11\]\/90 *,
    html:not(.dark) .bg-\[\#002b11\]\/80 * {
        color: #059669 !important;
    }

    /* Blue / Info */
    html.light .bg-\[\#001733\],
    html.light .bg-\[\#001733\]\/90,
    html:not(.dark) .bg-\[\#001733\],
    html:not(.dark) .bg-\[\#001733\]\/90 {
        background-color: #eff6ff !important;
        border-color: #bfdbfe !important;
        color: #2563eb !important;
    }
    html.light .bg-\[\#001733\] *,
    html.light .bg-\[\#001733\]\/90 *,
    html:not(.dark) .bg-\[\#001733\] *,
    html:not(.dark) .bg-\[\#001733\]\/90 * {
        color: #2563eb !important;
    }

    /* Amber / Warning */
    html.light .bg-\[\#2b1e00\],
    html.light .bg-\[\#2b1e00\]\/90,
    html.light .bg-\[\#1a1400\],
    html:not(.dark) .bg-\[\#2b1e00\],
    html:not(.dark) .bg-\[\#2b1e00\]\/90,
    html:not(.dark) .bg-\[\#1a1400\] {
        background-color: #fffbeb !important;
        border-color: #fde68a !important;
        color: #d97706 !important;
    }
    html.light .bg-\[\#2b1e00\] *,
    html.light .bg-\[\#2b1e00\]\/90 *,
    html.light .bg-\[\#1a1400\] *,
    html:not(.dark) .bg-\[\#2b1e00\] *,
    html:not(.dark) .bg-\[\#2b1e00\]\/90 *,
    html:not(.dark) .bg-\[\#1a1400\] * {
        color: #d97706 !important;
    }

    /* Red / Danger */
    html.light .bg-\[\#2b0000\],
    html.light .bg-\[\#2b0000\]\/90,
    html.light .bg-\[\#3b0000\],
    html:not(.dark) .bg-\[\#2b0000\],
    html:not(.dark) .bg-\[\#2b0000\]\/90,
    html:not(.dark) .bg-\[\#3b0000\] {
        background-color: #fef2f2 !important;
        border-color: #fecaca !important;
        color: #dc2626 !important;
    }
    html.light .bg-\[\#2b0000\] *,
    html.light .bg-\[\#2b0000\]\/90 *,
    html.light .bg-\[\#3b0000\] *,
    html:not(.dark) .bg-\[\#2b0000\] *,
    html:not(.dark) .bg-\[\#2b0000\]\/90 *,
    html:not(.dark) .bg-\[\#3b0000\] * {
        color: #dc2626 !important;
    }

    /* Purple / Violet */
    html.light .bg-\[\#1a0033\],
    html.light .bg-\[\#270038\],
    html:not(.dark) .bg-\[\#1a0033\],
    html:not(.dark) .bg-\[\#270038\] {
        background-color: #faf5ff !important;
        border-color: #e9d5ff !important;
        color: #9333ea !important;
    }
    html.light .bg-\[\#1a0033\] *,
    html.light .bg-\[\#270038\] *,
    html:not(.dark) .bg-\[\#1a0033\] *,
    html:not(.dark) .bg-\[\#270038\] * {
        color: #9333ea !important;
    }

    /* Status Badge Borders */
    html.light .border-\[\#005522\], html:not(.dark) .border-\[\#005522\] { border-color: #a7f3d0 !important; }
    html.light .border-\[\#003b80\], html:not(.dark) .border-\[\#003b80\] { border-color: #bfdbfe !important; }
    html.light .border-\[\#553c00\], html:not(.dark) .border-\[\#553c00\] { border-color: #fde68a !important; }
    html.light .border-\[\#550000\], html:not(.dark) .border-\[\#550000\] { border-color: #fecaca !important; }
    html.light .border-\[\#581c87\], html.light .border-\[\#4a1d96\], html:not(.dark) .border-\[\#581c87\], html:not(.dark) .border-\[\#4a1d96\] { border-color: #e9d5ff !important; }

    /* High-contrast status colors in light mode */
    html.light .text-\[\#50e3c2\], html:not(.dark) .text-\[\#50e3c2\] { color: #059669 !important; }
    html.light .text-\[\#ff453a\], html:not(.dark) .text-\[\#ff453a\] { color: #dc2626 !important; }
    html.light .text-\[\#f5a623\], html:not(.dark) .text-\[\#f5a623\] { color: #d97706 !important; }
    html.light .text-\[\#c084fc\], html.light .text-\[\#a855f7\], html:not(.dark) .text-\[\#c084fc\], html:not(.dark) .text-\[\#a855f7\] { color: #9333ea !important; }

    /* Status indicator dot colors */
    html.light .bg-\[\#50e3c2\], html:not(.dark) .bg-\[\#50e3c2\] { background-color: #10b981 !important; }
    html.light .bg-\[\#ff453a\], html:not(.dark) .bg-\[\#ff453a\] { background-color: #ef4444 !important; }
    html.light .bg-\[\#f5a623\], html:not(.dark) .bg-\[\#f5a623\] { background-color: #f59e0b !important; }

    /* Secondary metadata badges (e.g. Jam, Tanggal, Durasi) */
    html.light .bg-\[\#161616\] { background-color: #f4f4f5 !important; }
    html.light .bg-\[\#161616\] .text-\[\#a1a1a1\], html:not(.dark) .bg-\[\#161616\] .text-\[\#a1a1a1\] { color: #3f3f46 !important; }
    html.light .bg-\[\#161616\] .text-\[\#888888\], html:not(.dark) .bg-\[\#161616\] .text-\[\#888888\] { color: #52525b !important; }

    /* Flash Status Notification Banners */
    html.light .border-\[\#005522\],
    html:not(.dark) .border-\[\#005522\] {
        border-color: #a7f3d0 !important;
    }
    html.light .border-\[\#553c00\],
    html:not(.dark) .border-\[\#553c00\] {
        border-color: #fde68a !important;
    }
    html.light .border-\[\#550000\],
    html:not(.dark) .border-\[\#550000\] {
        border-color: #fecaca !important;
    }

    /* Dropdown Menus & Modals Shadow */
    html.light .custom-dropdown-menu,
    html:not(.dark) .custom-dropdown-menu {
        background-color: #ffffff !important;
        border-color: #e5e5e5 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
    }
</style>
