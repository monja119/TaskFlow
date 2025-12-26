<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TaskFlow') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center py-10">
    <div class="relative w-full max-w-6xl px-4">
        <div class="absolute inset-0 blur-3xl opacity-60 bg-[radial-gradient(circle_at_20%_20%,#22d3ee33,transparent_40%),radial-gradient(circle_at_80%_10%,#34d39933,transparent_45%),radial-gradient(circle_at_70%_70%,#6366f133,transparent_45%)]"></div>

        <div class="relative bg-white/5 border border-white/10 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl">
            <header class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="TaskFlow" class="h-12 w-12 rounded-xl bg-slate-900/80 p-2 border border-white/10">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-300">TaskFlow</p>
                        <p class="text-sm text-slate-400">Organisez. Priorisez. Avancez.</p>
                    </div>
                </div>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-400 to-cyan-400 text-slate-900 font-semibold shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition">Se connecter</a>
                @endif
            </header>

            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8 items-end">
                <div class="space-y-4">
                    <h1 class="text-3xl sm:text-4xl font-semibold leading-tight">Coordonnez vos projets avec clarté et rythme.</h1>
                    <p class="text-slate-300 text-base">Visualisez progression, priorités et risques en un seul écran. TaskFlow garde l'équipe alignée, du manager au contributeur.</p>
                    <div class="flex flex-wrap items-center gap-3">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-400 to-cyan-300 text-slate-900 font-semibold shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/35 transition">Ouvrir la plateforme</a>
                        @endif
                        <span class="text-sm text-slate-400">⏱️ Prise en main 2 min · 🔒 Sanctum prêt</span>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-3">
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-lg font-semibold">Vue 360°</p>
                            <p class="text-sm text-slate-400">Projets, tâches, risques en un coup d'œil.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-lg font-semibold">Flow maîtrisé</p>
                            <p class="text-sm text-slate-400">Filtres rapides : priorité, statut, retard.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-lg font-semibold">API prête</p>
                            <p class="text-sm text-slate-400">Endpoints sécurisés pour mobile & intégrations.</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-400">Projets actifs</p>
                            <p class="text-3xl font-semibold">14</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Tâches on-time</p>
                            <p class="text-3xl font-semibold text-emerald-300">92%</p>
                        </div>
                    </div>
                    <div class="space-y-3 text-sm text-slate-300">
                        <div class="flex gap-3">
                            <span class="h-9 w-9 rounded-xl bg-emerald-400/15 text-emerald-200 flex items-center justify-center border border-emerald-300/30">⚡</span>
                            <div>
                                <p class="font-semibold text-slate-100">Priorités claires</p>
                                <p>Badges haute/moyenne/basse + actions rapides.</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <span class="h-9 w-9 rounded-xl bg-cyan-400/15 text-cyan-200 flex items-center justify-center border border-cyan-300/30">📅</span>
                            <div>
                                <p class="font-semibold text-slate-100">Echéances sécurisées</p>
                                <p>Alertes visuelles sur retards et échéances proches.</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <span class="h-9 w-9 rounded-xl bg-indigo-400/15 text-indigo-200 flex items-center justify-center border border-indigo-300/30">🛡️</span>
                            <div>
                                <p class="font-semibold text-slate-100">Rôles & permissions</p>
                                <p>Admin, Manager, Membre avec policies fines.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center pt-2">
                        <a href="/admin" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/15 text-slate-100 hover:border-white/30 transition">Connexion</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
