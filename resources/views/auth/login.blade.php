<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | AI Store Health Monitor</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&family=jetbrains-mono:400,500&display=swap"
        rel="stylesheet" />
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --obsidian-bg: #020617;
            --obsidian-card: #1e293b;
            --accent-primary: #10b981;
        }

        body {
            background-color: var(--obsidian-bg);
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="antialiased min-h-screen flex items-center justify-center p-4 relative overflow-hidden text-slate-300">

    <!-- Background Elements -->
    <div
        class="fixed inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-[#020617] to-[#020617] -z-20">
    </div>
    <div class="fixed inset-0 z-0 opacity-20 pointer-events-none"
        style="background-image: linear-gradient(#1e293b 1px, transparent 1px), linear-gradient(90deg, #1e293b 1px, transparent 1px); background-size: 30px 30px;">
    </div>

    <!-- Card -->
    <div
        class="w-full max-w-md bg-slate-900/50 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl p-8 relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 group">
                <div
                    class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center group-hover:bg-emerald-500/20 transition">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="font-bold text-xl text-white">Health<span class="text-emerald-500">Monitor</span></span>
            </a>
        </div>

        <h2 class="text-2xl font-bold text-white mb-6 text-center">Sign in to your dashboard</h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-400 mb-2">Email Address</label>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                    class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition outline-none"
                    placeholder="name@company.com">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-medium text-slate-400">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-emerald-500 hover:text-emerald-400 transition">Forgot password?</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition outline-none pr-10"
                        placeholder="••••••••">
                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-300 transition focus:outline-none">
                        <!-- Eye icon -->
                        <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye slash icon -->
                        <svg id="eyeSlashIcon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500/50">
                <label for="remember_me" class="ml-2 text-sm text-slate-400">Keep me logged in</label>
            </div>

            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-lg shadow-lg shadow-emerald-900/20 transition-all hover:scale-[1.02]">
                Sign In
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-slate-500">
            Don't have an account?
            <a href="{{ route('register') }}"
                class="text-emerald-400 hover:text-emerald-300 font-medium transition">Start free trial</a>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');
        const eyeSlashIcon = document.querySelector('#eyeSlashIcon');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function (e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                eyeIcon.classList.toggle('hidden');
                eyeSlashIcon.classList.toggle('hidden');
            });
        }
    </script>
</body>

</html>