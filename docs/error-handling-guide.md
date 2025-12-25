# Guide Pratique - Gestion des Erreurs

## 🚀 Démarrage Rapide

### Utilisation simple

Les erreurs sont gérées automatiquement. Aucune configuration n'est nécessaire - il suffit de laisser Laravel gérer les exceptions comme d'habitude.

```php
// Ces exceptions seront automatiquement affichées avec votre page personnalisée
throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
throw new \Illuminate\Auth\AuthenticationException();
throw new \Illuminate\Auth\Access\AuthorizationException();
```

## 📝 Personnalisation des Messages

### Éditer les messages par défaut

Modifiez `config/errors.php`:

```php
'messages' => [
    404 => [
        'title' => 'Mon titre personnalisé',
        'description' => 'Ma description personnalisée',
        'icon' => '🎯'
    ],
]
```

### Ajouter un nouveau code d'erreur

```php
'messages' => [
    418 => [  // I'm a teapot 😄
        'title' => 'Je suis une théière',
        'description' => 'Cette ressource est une théière.',
        'icon' => '🫖'
    ],
]
```

## 🎨 Personnalisation du Design

### Modifier les couleurs

Éditez `resources/views/errors/error.blade.php` et changez les variables CSS:

```css
body {
    background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
}

.error-header {
    background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
}
```

### Ajouter un logo

Dans le template, ajoutez après `<span class="error-icon">{{ $error['icon'] }}</span>`:

```html
<img src="/images/logo.png" alt="Logo" style="width: 80px; margin-bottom: 20px;">
```

## 🛡️ Exceptions Personnalisées

### Créer une exception métier

```php
namespace App\Exceptions;

use Exception;

class ProjectNotFoundException extends BusinessException
{
    public function __construct(string $message = 'Projet non trouvé')
    {
        parent::__construct($message, 404);
    }
}
```

### Utiliser votre exception

```php
public function show($id)
{
    $project = Project::find($id);
    
    if (!$project) {
        throw new ProjectNotFoundException();
    }
    
    return view('projects.show', compact('project'));
}
```

## 🔧 Ajouter de la Logique Personnalisée

### Éxtendre le Handler

```php
namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends \App\Exceptions\Handler
{
    public function render(Request $request, Throwable $exception): Response
    {
        // Votre logique personnalisée ici
        if ($exception instanceof YourCustomException) {
            // Logique spécifique
        }

        return parent::render($request, $exception);
    }
}
```

## 📊 Exceptions Filament Intégrées

Le système gère automatiquement les exceptions Filament:

```php
// Authentification Filament
// → Affiche votre page d'erreur personnalisée 401

// Autorisation Filament
// → Affiche votre page d'erreur personnalisée 403

// Validation Filament
// → Affiche votre page d'erreur personnalisée 422
```

## 🐛 Mode Débogage

### En développement (APP_DEBUG=true)

Les détails de débogage sont accessibles via un bouton "Afficher les détails":

- Message d'erreur exact
- Fichier et ligne
- Stack trace (5 premiers appels)

### En production (APP_DEBUG=false)

Aucun détail de débogage n'est affiché, pour des raisons de sécurité.

### Forcer l'affichage des détails

```php
// Dans config/errors.php
'show_debug' => true,  // Toujours afficher les détails (ne pas faire en production!)
```

## 🧪 Tester les Pages d'Erreur

### Route de test (à ajouter pour le développement)

```php
// routes/web.php
if (config('app.debug')) {
    Route::get('/error-test/{code}', function ($code) {
        abort((int)$code);
    });
}
```

### Accéder aux pages de test

```
http://localhost:8000/error-test/404
http://localhost:8000/error-test/500
http://localhost:8000/error-test/403
```

## 📈 Rapporter les Erreurs

### Configuration de rapports d'erreurs

```php
// Dans config/errors.php
'report' => [
    'enabled' => true,
    'channel' => 'slack',  // ou 'mail', 'sentry', etc.
]
```

### Dans le Handler

```php
public function register(): void
{
    $this->reportable(function (Throwable $e) {
        if (config('errors.report.enabled')) {
            \Log::error('Application Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    });
}
```

## 🔍 Déboguer les Erreurs

### Afficher la trace complète en dev

Le fichier `error.blade.php` affiche les 5 premiers appels de stack. Pour plus, consultez `storage/logs/laravel.log`.

### Vérifier les logs

```bash
tail -f storage/logs/laravel.log
```

## 🎯 Bonnes Pratiques

### ✅ À faire

- Utiliser des exceptions spécifiques pour différents scénarios
- Documenter les exceptions que votre code peut lever
- Tester les cas d'erreur
- Logguer les erreurs importantes

### ❌ À éviter

- Afficher les traces complètes en production
- Exposer les chemins de fichiers au client
- Capturer toutes les exceptions sans les traiter
- Ignorer les erreurs silencieusement

## 📚 Exemple Complet

```php
// app/Services/ProjectService.php
class ProjectService
{
    /**
     * Obtenir un projet
     * 
     * @throws ProjectNotFoundException
     * @throws UnauthorizedException
     */
    public function getProject(int $id): Project
    {
        $project = Project::find($id);

        if (!$project) {
            throw new ProjectNotFoundException(
                "Le projet #$id n'existe pas"
            );
        }

        if (!auth()->user()->can('view', $project)) {
            throw new UnauthorizedException(
                'Vous n\'avez pas accès à ce projet'
            );
        }

        return $project;
    }
}

// Dans votre contrôleur ou action Filament
try {
    $project = $this->projectService->getProject($id);
} catch (ProjectNotFoundException $e) {
    // Le Handler s'en charge automatiquement
    throw $e;
}
```

## 📞 Support et Ressources

Pour plus d'informations, consultez:
- `docs/error-handling.md` - Documentation complète
- `app/Services/Error/ErrorFormatterService.php` - Code source
- `app/Exceptions/Handler.php` - Gestionnaire global
- `config/errors.php` - Configuration

---

**Besoin d'aide?** Consultez la documentation technique complète ou les tests unitaires pour des exemples supplémentaires.
