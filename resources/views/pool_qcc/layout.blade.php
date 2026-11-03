<!DOCTYPE html>
<html lang="id" class="light" id="htmlRoot">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Pool QCC - Quality Control Circle Challenge')</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">

        <link rel="shortcut icon" href="{{ url('/') }}/assets/images/logo-no-teks.png" />

        <!-- Immediate Theme Initialization to prevent flash -->
        <script>
            (function() {
                try {
                    const savedTheme = localStorage.getItem('pool_qcc_theme') || 'light';
                    const root = document.documentElement;
                    if (savedTheme === 'dark') {
                        root.classList.remove('light');
                        root.classList.add('dark');
                    } else {
                        root.classList.remove('dark');
                        root.classList.add('light');
                    }
                } catch (e) {}
            })();
        </script>

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                            heading: ['"Outfit"', 'sans-serif'],
                            game: ['"Fredoka"', '"Outfit"', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#eef2ff',
                                100: '#e0e7ff',
                                500: '#6366f1',
                                600: '#4f46e5',
                                700: '#4338ca',
                                800: '#3730a3',
                                900: '#312e81',
                            }
                        }
                    }
                }
            }
        </script>

        <!-- FontAwesome 6 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- jQuery 3.7.1 -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Canvas Confetti for Game Rewards -->
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

        <style>
            /* SweetAlert2 Popup must ALWAYS be on top of every modal */
            .swal2-container {
                z-index: 9999999 !important;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            h1,
            h2,
            h3,
            h4,
            .font-heading {
                font-family: 'Outfit', sans-serif;
            }

            /* Light Theme Defaults (Jelas, Terang, Bersih) */
            html.light body {
                background-color: #f1f5f9;
                color: #1e293b;
            }

            html.light .glass-panel {
                background: rgba(255, 255, 255, 0.96);
                border: 1px solid #e2e8f0;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            }

            html.light .glass-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
                transition: all 0.2s ease-in-out;
            }

            html.light .glass-card:hover {
                border-color: #6366f1;
                box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1), 0 4px 6px -4px rgba(99, 102, 241, 0.1);
                transform: translateY(-2px);
            }

            /* Dark Theme Fallback */
            html.dark body {
                background-color: #0b1120;
                color: #f8fafc;
            }

            html.dark .glass-panel {
                background: rgba(17, 24, 39, 0.85);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            }

            html.dark .glass-card {
                background: rgba(30, 41, 59, 0.65);
                border: 1px solid rgba(255, 255, 255, 0.06);
                transition: all 0.2s ease-in-out;
            }

            html.dark .glass-card:hover {
                border-color: rgba(99, 102, 241, 0.4);
                transform: translateY(-2px);
            }
        </style>
        @yield('styles')
    </head>

    <body class="flex flex-col min-h-screen antialiased selection:bg-indigo-500 selection:text-white">

        <!-- Ambient Subtle Background Glow for Depth -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0 opacity-30 dark:opacity-20">
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 -right-32 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-purple-500/15 rounded-full blur-3xl"></div>
        </div>

        @if (!View::hasSection('hide_nav'))
            <!-- Top Navigation Header -->
            <header
                class="sticky top-0 z-30 glass-panel border-b border-slate-200 dark:border-slate-800 transition-colors">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Brand Logo & Title -->
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('pool-qcc.portal-home') }}" class="flex items-center space-x-3 group">
                                <img src="{{ asset('assets/images/logo-no-teks.png') }}" alt="Logo"
                                    class="w-10 h-10 object-contain group-hover:scale-105 transition-transform" />
                                <div>
                                    <span
                                        class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">POOL
                                        QCC</span>
                                    <span
                                        class="hidden sm:inline-block ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300 dark:border-indigo-500/30">Exhibition
                                        Hub</span>
                                </div>
                            </a>
                        </div>

                        <!-- Navigation Tabs -->
                        <nav class="hidden md:flex items-center space-x-1 font-medium text-sm">
                            <a href="{{ route('pool-qcc.booths-index') }}"
                                class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('pool-qcc.booth*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                <i class="fa-solid fa-store mr-1.5 text-indigo-500"></i> Layar Booth
                            </a>
                            <a href="{{ route('pool-qcc.leaderboard') }}"
                                class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('pool-qcc.leaderboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                <i class="fa-solid fa-ranking-star mr-1.5 text-amber-500"></i> Live Leaderboard
                            </a>

                            <!-- Admin Dropdown (Fixed Hover Gap + Smooth Buffer) -->
                            <div class="relative group" id="adminDropdownContainer">
                                <a href="{{ route('pool-qcc.admin.dashboard') }}" id="adminDropdownBtn"
                                    class="px-3.5 py-2 rounded-xl transition-all flex items-center space-x-1 {{ request()->routeIs('pool-qcc.admin.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-slate-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                    <i class="fa-solid fa-sliders mr-1.5 text-cyan-600 dark:text-cyan-400"></i>
                                    <span>Admin Panel</span>
                                    <i
                                        class="fa-solid fa-chevron-down text-xs ml-1 text-slate-400 transition-transform duration-200 group-hover:rotate-180"></i>
                                </a>
                                <!-- Dropdown container with pt-2 bridging the hover gap seamlessly -->
                                <div id="adminDropdownMenu"
                                    class="absolute right-0 top-full pt-2 w-56 z-50 hidden group-hover:block transition-all">
                                    <div
                                        class="rounded-2xl glass-panel shadow-2xl py-2 border border-slate-200 dark:border-slate-700/80 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md">
                                        <a href="{{ route('pool-qcc.admin.dashboard') }}"
                                            class="flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-indigo-50 dark:hover:bg-indigo-600/30 transition-colors">
                                            <i class="fa-solid fa-chart-pie w-5 text-indigo-500 mr-2"></i> Dashboard
                                            Overview
                                        </a>
                                        <a href="{{ route('pool-qcc.admin.booths') }}"
                                            class="flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-indigo-50 dark:hover:bg-indigo-600/30 transition-colors">
                                            <i class="fa-solid fa-store w-5 text-cyan-500 mr-2"></i> Kelola Booth
                                        </a>
                                        <a href="{{ route('pool-qcc.admin.questions') }}"
                                            class="flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-indigo-50 dark:hover:bg-indigo-600/30 transition-colors">
                                            <i class="fa-solid fa-clipboard-question w-5 text-amber-500 mr-2"></i> Bank
                                            Soal Booth
                                        </a>
                                        <a href="{{ route('pool-qcc.admin.participants') }}"
                                            class="flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-indigo-50 dark:hover:bg-indigo-600/30 transition-colors">
                                            <i class="fa-solid fa-users w-5 text-emerald-500 mr-2"></i> Data Peserta
                                        </a>
                                        <a href="{{ route('pool-qcc.admin.votes') }}"
                                            class="flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-indigo-50 dark:hover:bg-indigo-600/30 transition-colors">
                                            <i class="fa-solid fa-receipt w-5 text-rose-500 mr-2"></i> Log Jawaban /
                                            Votes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </nav>

                        <!-- Right Actions: Theme Toggle & Exit/Back -->
                        <div class="flex items-center space-x-2.5">
                            <!-- Theme Switcher Button (Light / Dark) -->
                            <button type="button" id="themeToggleBtn" onclick="toggleTheme()"
                                class="py-1 px-4 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition"
                                title="Ganti Tema">
                                <i id="themeToggleIcon" class="fa-solid fa-moon text-sm"></i>
                            </button>

                            @if (Session::has('pool_participant_id'))
                                <form action="{{ route('pool-qcc.exit') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 dark:bg-rose-500/20 dark:text-rose-300 dark:border-rose-500/30 dark:hover:bg-rose-500/30 transition-all flex items-center">
                                        <i class="fa-solid fa-arrow-right-from-bracket mr-1.5"></i> Exit Sesi Tap
                                    </button>
                                </form>
                            @endif
                            <a href="{{ url('/') }}"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition">
                                <i class="fa-solid fa-house mr-1.5"></i> Portal BAS
                            </a>
                        </div>
                    </div>
                </div>
            </header>
        @endif

        <!-- Main Content Area -->
        <main class="flex-1">
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div
                        class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-300 flex items-center space-x-3 shadow-sm">
                        <i
                            class="fa-solid fa-circle-check text-xl flex-shrink-0 text-emerald-600 dark:text-emerald-400"></i>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div
                        class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 dark:bg-rose-500/10 dark:border-rose-500/30 dark:text-rose-300 flex items-center space-x-3 shadow-sm">
                        <i
                            class="fa-solid fa-triangle-exclamation text-xl flex-shrink-0 text-rose-600 dark:text-rose-400"></i>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div
                        class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 dark:bg-amber-500/10 dark:border-amber-500/30 dark:text-amber-300 flex items-center space-x-3 shadow-sm">
                        <i
                            class="fa-solid fa-circle-exclamation text-xl flex-shrink-0 text-amber-600 dark:text-amber-400"></i>
                        <span class="font-medium text-sm">{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        @if (!View::hasSection('hide_nav'))
            <!-- Footer -->
            <footer
                class="glass-panel border-t border-slate-200 dark:border-slate-800/80 py-4 mt-auto relative z-10 text-xs text-slate-500 dark:text-slate-400 text-center">
                <div
                    class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-300">Pool QCC</span> &copy;
                        {{ date('Y') }} PT Bumi Alam Segar - QCC Innovation Challenge
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center"><i
                                class="fa-solid fa-circle text-[8px] text-emerald-500 mr-1.5 animate-pulse"></i> RFID
                            Reader
                            Ready</span>
                        <span>Maksimal 10 Booth / Peserta</span>
                    </div>
                </div>
            </footer>
        @endif

        <!-- Theme Switcher Script -->
        <script>
            const htmlRoot = document.getElementById('htmlRoot') || document.documentElement;

            function applyTheme(theme) {
                const isDark = (theme === 'dark');
                if (isDark) {
                    htmlRoot.classList.remove('light');
                    htmlRoot.classList.add('dark');
                } else {
                    htmlRoot.classList.remove('dark');
                    htmlRoot.classList.add('light');
                }
                try {
                    localStorage.setItem('pool_qcc_theme', theme);
                } catch (e) {}

                // Update all theme icons and text across navbar, floating bars, etc.
                document.querySelectorAll('.theme-icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
                document.querySelectorAll('.theme-icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
                document.querySelectorAll('.theme-label').forEach(el => {
                    el.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
                });

                const legacyIcon = document.getElementById('themeToggleIcon');
                if (legacyIcon) {
                    legacyIcon.className = isDark ? 'fa-solid fa-sun text-amber-400 text-sm' :
                        'fa-solid fa-moon text-slate-600 text-sm';
                }

                window.dispatchEvent(new CustomEvent('poolQccThemeChange', {
                    detail: {
                        theme: theme,
                        isDark: isDark
                    }
                }));
            }

            function toggleTheme() {
                const isDark = htmlRoot.classList.contains('dark');
                applyTheme(isDark ? 'light' : 'dark');
            }

            window.applyTheme = applyTheme;
            window.toggleTheme = toggleTheme;

            // Initialize Theme from localStorage (Default: Light)
            const savedTheme = localStorage.getItem('pool_qcc_theme') || 'light';
            applyTheme(savedTheme);

            // Admin dropdown hover buffer (prevents jitter / accidental closes)
            const adminContainer = document.getElementById('adminDropdownContainer');
            const adminMenu = document.getElementById('adminDropdownMenu');
            let adminDropdownTimer;

            if (adminContainer && adminMenu) {
                adminContainer.addEventListener('mouseenter', () => {
                    clearTimeout(adminDropdownTimer);
                    adminMenu.classList.remove('hidden');
                });

                adminContainer.addEventListener('mouseleave', () => {
                    adminDropdownTimer = setTimeout(() => {
                        adminMenu.classList.add('hidden');
                    }, 300); // 300ms buffer ensures smooth cursor transition
                });
            }
        </script>

        @yield('modals')
        @stack('modals')

        @yield('scripts')
    </body>

</html>
