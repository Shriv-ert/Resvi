<button 
    type="button" 
    onclick="toggleAppTheme()" 
    class="theme-toggle-btn p-1.5 rounded-[4px] border border-[#333333] hover:border-[#555555] bg-[#111111] hover:bg-[#161616] text-[#a1a1a1] hover:text-white transition flex items-center justify-center cursor-pointer shadow-none"
    title="Ganti Tema (Dark / Light)"
    aria-label="Ganti Tema"
>
    <!-- Sun Icon: Visible in Dark Mode, click to switch to Light Mode -->
    <svg class="theme-icon-sun w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    <!-- Moon Icon: Visible in Light Mode, click to switch to Dark Mode -->
    <svg class="theme-icon-moon w-3.5 h-3.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
</button>

<script>
    if (typeof window.toggleAppTheme === 'undefined') {
        window.toggleAppTheme = function() {
            var isDark = document.documentElement.classList.contains('dark');
            var newTheme = isDark ? 'light' : 'dark';
            
            if (newTheme === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
            
            // Set persistent cookie for 1 year
            document.cookie = "theme=" + encodeURIComponent(newTheme) + "; path=/; max-age=31536000; SameSite=Lax";
            
            window.syncThemeToggleIcons(newTheme);
        };

        window.syncThemeToggleIcons = function(currentTheme) {
            if (!currentTheme) {
                var match = document.cookie.match(/(?:^|; )theme=([^;]*)/);
                currentTheme = match ? decodeURIComponent(match[1]) : (document.documentElement.classList.contains('light') ? 'light' : 'dark');
            }
            
            var sunIcons = document.querySelectorAll('.theme-icon-sun');
            var moonIcons = document.querySelectorAll('.theme-icon-moon');
            var buttons = document.querySelectorAll('.theme-toggle-btn');
            
            if (currentTheme === 'light') {
                sunIcons.forEach(function(el) { el.classList.add('hidden'); });
                moonIcons.forEach(function(el) { el.classList.remove('hidden'); });
                buttons.forEach(function(btn) {
                    btn.setAttribute('title', 'Ganti ke Mode Gelap');
                    btn.setAttribute('aria-label', 'Ganti ke Mode Gelap');
                });
            } else {
                sunIcons.forEach(function(el) { el.classList.remove('hidden'); });
                moonIcons.forEach(function(el) { el.classList.add('hidden'); });
                buttons.forEach(function(btn) {
                    btn.setAttribute('title', 'Ganti ke Mode Terang');
                    btn.setAttribute('aria-label', 'Ganti ke Mode Terang');
                });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                window.syncThemeToggleIcons();
            });
        } else {
            window.syncThemeToggleIcons();
        }
    } else {
        // If script already declared, immediately sync newly rendered buttons
        window.syncThemeToggleIcons();
    }
</script>

