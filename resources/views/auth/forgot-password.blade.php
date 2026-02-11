<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | AI Store Health Monitor</title>
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

        <h2 class="text-2xl font-bold text-white mb-2 text-center">Reset Password</h2>
        <p class="text-slate-500 text-center text-sm mb-6 max-w-sm mx-auto">
            Enter your email address and we'll send you a link to reset your password.
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-400 mb-2">Email Address</label>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                    class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 transition outline-none"
                    placeholder="name@company.com">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-lg shadow-lg shadow-emerald-900/20 transition-all hover:scale-[1.02]">
                    Email Reset Link
                </button>
            </div>
        </form>

        <div class="mt-8 text-center text-sm text-slate-500">
            <a href="{{ route('login') }}"
                class="text-emerald-400 hover:text-emerald-300 font-medium transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Sign In
            </a>
        </div>
    </div>
</body>

</html>