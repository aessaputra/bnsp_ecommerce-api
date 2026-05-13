<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Ecommerce API</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="min-h-dvh bg-slate-950 font-sans text-slate-100 antialiased selection:bg-amber-300 selection:text-slate-950">
        <main class="relative isolate flex min-h-dvh items-center justify-center overflow-hidden px-6 py-12 sm:px-8">
            <div class="absolute inset-0 -z-20 bg-[radial-gradient(circle_at_top_left,rgba(245,158,11,0.26),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.20),transparent_34%),linear-gradient(135deg,#020617_0%,#0f172a_50%,#111827_100%)]"></div>
            <div class="absolute left-1/2 top-1/2 -z-10 h-[28rem] w-[28rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-amber-400/10 blur-3xl"></div>

            <section class="w-full max-w-xl overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/40 backdrop-blur-xl">
                <div class="h-1.5 bg-gradient-to-r from-amber-300 via-orange-400 to-sky-400"></div>

                <div class="p-7 sm:p-9">
                    <div class="flex items-start justify-between gap-5">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-300 text-slate-950 shadow-lg shadow-amber-500/25">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 7.5L12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M4 7.5 12 12l8-4.5M12 12v9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-200">
                            API Online
                        </span>
                    </div>

                    <div class="mt-8 space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-200/90">Ecommerce API</p>
                        <h1 class="max-w-md text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                            Welcome to the commerce backend.
                        </h1>
                        <p class="max-w-lg text-base leading-8 text-slate-300">
                            Project API Laravel sederhana untuk pengelolaan produk ecommerce.
                        </p>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Framework</p>
                            <p class="mt-2 text-sm font-semibold text-white">Laravel {{ Illuminate\Foundation\Application::VERSION }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Runtime</p>
                            <p class="mt-2 text-sm font-semibold text-white">PHP {{ PHP_VERSION }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                            <p class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Endpoint</p>
                            <p class="mt-2 text-sm font-semibold text-white">/api/products</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
