<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email | AI Store Health Monitor</title>
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

        <h2 class="text-2xl font-bold text-white mb-2 text-center">Verify Email</h2>
        <p class="text-slate-500 text-center text-sm mb-6">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you?
        </p>

        @if (session('status') == 'verification-link-sent')
            <div
                class="mb-4 font-medium text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-lg text-center">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-lg shadow-lg shadow-emerald-900/20 transition-all hover:scale-[1.02]">
                    {{ __('Resend Verification Email') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf

                <button type="submit" class="underline text-sm text-slate-500 hover:text-white transition">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</body>

</html>