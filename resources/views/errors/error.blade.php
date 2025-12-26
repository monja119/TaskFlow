<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $error['status'] }} - {{ $error['title'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-up {
            animation: slideUp 0.5s ease-out;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-5 bg-gray-50">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full overflow-hidden animate-slide-up border border-gray-200">
        <div class="bg-gray-900 text-white py-10 px-5 text-center">
            <span class="block text-6xl mb-5">{{ $error['icon'] }}</span>
            <div class="text-5xl font-bold mb-2.5 tracking-wider">{{ $error['status'] }}</div>
            <h1 class="text-3xl font-semibold mb-4">{{ $error['title'] }}</h1>
        </div>

        <div class="py-10 px-8">
            <p class="text-base text-gray-600 leading-relaxed mb-8">{{ $error['description'] }}</p>

            @if($error['message'])
                <div class="bg-gray-50 border-l-4 border-gray-900 p-4 rounded mb-8 font-mono text-sm text-gray-800 break-words">
                    <strong>Message:</strong> {{ $error['message'] }}
                </div>
            @endif

            <div class="flex gap-4 justify-center flex-wrap">
                <a href="/" class="px-8 py-3 bg-gray-900 text-white rounded-md text-sm font-semibold transition-all duration-300 hover:bg-gray-800 hover:shadow-md inline-block">
                    Retour à l'accueil
                </a>
                <button type="button" class="px-8 py-3 bg-white text-gray-700 border border-gray-300 rounded-md text-sm font-semibold transition-all duration-300 hover:bg-gray-50" onclick="history.back()">
                    Page précédente
                </button>
            </div>

            @if($error['debug'])
                <button type="button" class="text-gray-700 cursor-pointer text-xs underline p-0 mt-4 hover:text-gray-900 bg-transparent border-0" onclick="toggleDebug()">
                    🔧 Afficher les détails de débogage
                </button>

                <div class="hidden bg-gray-50 border-t border-gray-200 p-8 mt-8" id="debug-section">
                    <div class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">Informations de Débogage</div>
                    <div class="font-mono text-xs text-gray-600 leading-relaxed">
                        <div class="mb-4 p-2.5 bg-white rounded border-l-4 border-gray-900">
                            <strong class="text-gray-900">Fichier:</strong> {{ $error['debug']['file'] }}
                        </div>
                        <div class="mb-4 p-2.5 bg-white rounded border-l-4 border-gray-900">
                            <strong class="text-gray-900">Ligne:</strong> {{ $error['debug']['line'] }}
                        </div>

                        @if($error['debug']['trace'])
                            <div class="mt-4 pt-4 border-t border-gray-300">
                                <strong class="block mb-2.5">Stack Trace:</strong>
                                @foreach($error['debug']['trace'] as $trace)
                                    <div class="my-2.5 p-2 bg-white rounded text-xs">
                                        <div class="text-gray-900 font-semibold">{{ $trace['file'] }}:{{ $trace['line'] }}</div>
                                        <div>{{ $trace['class'] ?? '' }}{{ $trace['class'] ? '::' : '' }}{{ $trace['function'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleDebug() {
            const section = document.getElementById('debug-section');
            if (section) {
                section.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
