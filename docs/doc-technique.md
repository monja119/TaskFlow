# TaskFlow — Documentation technique

> Organisez. Priorisez. Avancez intelligemment.

---

## Table des matières

1. [Introduction & Vision](#1-introduction--vision)
2. [Architecture globale](#2-architecture-globale)
3. [Stack technologique](#3-stack-technologique)
4. [Structure du projet](#4-structure-du-projet)
5. [Modèle de données](#5-modèle-de-données)
6. [Enums & Domain Layer](#6-enums--domain-layer)
7. [Migrations & schéma](#7-migrations--schéma)
8. [Models Eloquent](#8-models-eloquent)
9. [Policies & autorisations (RBAC)](#9-policies--autorisations-rbac)
10. [API REST](#10-api-rest)
11. [Administration Filament](#11-administration-filament)
12. [Design & thème UI](#12-design--thème-ui)
13. [Factories & seeders](#13-factories--seeders)
14. [Installation & configuration](#14-installation--configuration)
15. [Tests & qualité](#15-tests--qualité)
16. [Roadmap & fonctionnalités avancées](#16-roadmap--fonctionnalités-avancées)
17. [Troubleshooting](#17-troubleshooting)

---

## 1. Introduction & Vision

### Objectif
**TaskFlow** est une plateforme intelligente de gestion de projets et de tâches destinée aux équipes, freelances et managers. Elle vise à :
- Organiser le travail en projets structurés.
- Suivre l'avancement avec statuts, priorités, dates d'échéance.
- Améliorer la productivité grâce à une interface moderne (Filament) et des données enrichies (progression, risques).
- Offrir une API REST robuste pour intégrations externes (mobile, automation).

### Principes de conception
- **Clean Code** : séparation domain/application/infra, nommage explicite, pas de duplication.
- **SOLID** : Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion.
- **Clean Architecture** : couches découplées (Domain → Application → Infrastructure/UI).
- **Tests E2E & Feature** : garantir la stabilité et la non-régression.

### MVP (fonctionnalités actuelles)
- Gestion projets : CRUD, statut, dates, progression, risque, archivage.
- Gestion tâches : CRUD, priorité, statut, dates début/échéance, estimation, assignation.
- Utilisateurs : rôles (Admin, Manager, Membre), permissions basiques.
- Admin Filament : tables, formulaires, filtres, infolists.
- API REST : endpoints protégés avec validation, pagination, filtres.
- Design ultra-moderne : glassmorphism, dégradés, animations fluides.

---

## 2. Architecture globale

### Couches architecturales

```
┌─────────────────────────────────────────────┐
│          Présentation (UI/API)              │
│  - Filament Resources (admin panel)         │
│  - API Controllers (REST endpoints)         │
└─────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────┐
│           Application Layer                 │
│  - Form Requests (validation)               │
│  - Resources (API responses)                │
│  - Policies (authorization)                 │
│  - Events & Listeners (future)              │
└─────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────┐
│            Domain Layer                     │
│  - Enums (ProjectStatus, TaskStatus, etc.)  │
│  - Value Objects (future)                   │
│  - Domain Services (risk scoring, etc.)     │
└─────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────┐
│         Infrastructure Layer                │
│  - Eloquent Models (persistence)            │
│  - Migrations (schema)                      │
│  - Factories & Seeders (data generation)    │
└─────────────────────────────────────────────┘
```

### Principes SOLID appliqués
- **S** : chaque classe a une responsabilité unique (ex: `StoreProjectRequest` ne fait que valider).
- **O** : extensible via policies, events, services sans modifier le core.
- **L** : les resources Filament peuvent être étendues sans casser la base.
- **I** : interfaces spécifiques (à venir pour repositories si nécessaire).
- **D** : dépendance sur abstractions (policies, contracts Laravel).

---

## 3. Stack technologique

### Backend
- **PHP** : ^8.2 (typage strict, enums natifs, readonly properties)
- **Laravel** : 12.x (dernière version stable)
  - Eloquent ORM
  - Validation & Form Requests
  - Policies & Gates
  - Queues & Jobs (préparé pour notifications)
  - Soft Deletes

### Admin Panel
- **Filament** : v4
  - Resources (CRUD générés)
  - Tables, Forms, Infolists
  - Actions, Filters, Bulk actions
  - Widgets (dashboard KPI à venir)
  - Customizable theme

### Frontend
- **Tailwind CSS** : v4 (via `@import 'tailwindcss'`)
- **Vite** : build tool moderne
- **Alpine.js** : (inclus via Filament pour interactivité)
- **Livewire** : (framework Filament)

### Base de données
- **MySQL/PostgreSQL** : recommandé (support SQLite dev)
- **Migrations versionnées** : rollback sécurisé
- **Indexes** : sur clés étrangères, statuts, dates

### Outils dev & qualité
- **Composer** : gestion dépendances PHP
- **NPM** : dépendances JS
- **Laravel Pint** : code style (PSR-12)
- **PHPUnit** : tests unitaires & feature
- **Laravel Dusk** : tests E2E navigateur (à ajouter)

---

## 4. Structure du projet

```
TaskFlow/
├── app/
│   ├── Enums/                      # Domain enums
│   │   ├── ProjectStatus.php
│   │   ├── TaskStatus.php
│   │   ├── TaskPriority.php
│   │   └── UserRole.php
│   ├── Filament/
│   │   └── Resources/
│   │       ├── Projects/
│   │       │   ├── ProjectResource.php
│   │       │   ├── Pages/
│   │       │   │   ├── CreateProject.php
│   │       │   │   ├── EditProject.php
│   │       │   │   ├── ListProjects.php
│   │       │   │   └── ViewProject.php
│   │       │   ├── Schemas/
│   │       │   │   ├── ProjectForm.php
│   │       │   │   └── ProjectInfolist.php
│   │       │   └── Tables/
│   │       │       └── ProjectsTable.php
│   │       ├── Tasks/              # Idem structure
│   │       └── Users/              # Idem structure
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── API/
│   │   │       ├── ProjectController.php
│   │   │       └── TaskController.php
│   │   ├── Requests/
│   │   │   ├── StoreProjectRequest.php
│   │   │   ├── UpdateProjectRequest.php
│   │   │   ├── StoreTaskRequest.php
│   │   │   └── UpdateTaskRequest.php
│   │   └── Resources/
│   │       ├── ProjectResource.php
│   │       ├── TaskResource.php
│   │       └── UserResource.php
│   ├── Models/
│   │   ├── Project.php
│   │   ├── Task.php
│   │   └── User.php
│   ├── Policies/
│   │   ├── ProjectPolicy.php
│   │   └── TaskPolicy.php
│   └── Providers/
│       └── AppServiceProvider.php  # Register policies
├── database/
│   ├── factories/
│   │   ├── ProjectFactory.php
│   │   ├── TaskFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2025_12_19_091134_create_projects_table.php
│   │   ├── 2025_12_19_093250_create_tasks_table.php
│   │   ├── 2025_12_24_120000_add_tracking_fields_to_projects_and_tasks.php
│   │   └── 2025_12_24_131000_add_role_to_users.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── docs/                           # Documentation
│   ├── doc-technique.md
│   └── doc-utilisateur.md
├── resources/
│   ├── css/
│   │   └── app.css                 # Thème custom Tailwind + Filament
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       └── welcome.blade.php
├── routes/
│   ├── api.php                     # API routes (auth middleware)
│   ├── console.php
│   └── web.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── composer.json                   # Dépendances PHP
├── package.json                    # Dépendances NPM
├── phpunit.xml                     # Config tests
├── vite.config.js                  # Config Vite
└── README.md
```

---

## 5. Modèle de données

### Schéma entité-relation

```
┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│    Users     │         │   Projects   │         │    Tasks     │
├──────────────┤         ├──────────────┤         ├──────────────┤
│ id           │────┐    │ id           │────┐    │ id           │
│ name         │    │    │ name         │    │    │ title        │
│ email        │    │    │ description  │    │    │ description  │
│ password     │    │    │ status       │    │    │ priority     │
│ role         │    │    │ start_date   │    │    │ status       │
│ timestamps   │    │    │ end_date     │    │    │ start_date   │
│              │    │    │ progress     │    │    │ due_date     │
│              │    │    │ risk_score   │    │    │ completed_at │
│              │    │    │ archived_at  │    │    │ archived_at  │
│              │    └───→│ user_id (FK) │    │    │ estimate_min │
│              │         │ timestamps   │    │    │ actual_min   │
│              │         │ soft deletes │    └───→│ project_id   │
│              │         └──────────────┘         │ user_id (FK) │
│              │                                   │ timestamps   │
│              │                                   │ soft deletes │
└──────────────┘                                   └──────────────┘
      1:N                      1:N                       N:1
   (owner)                   (tasks)                 (assignee)
```

### Règles métier
1. Un **User** possède plusieurs **Projects** (relation `hasMany`).
2. Un **Project** contient plusieurs **Tasks** (relation `hasMany`).
3. Une **Task** appartient à un **Project** et est assignée à un **User** (relations `belongsTo`).
4. Soft deletes : suppression logique préserve l'historique.
5. Archivage : `archived_at` permet de masquer sans supprimer.

---

## 6. Enums & Domain Layer

### 6.1 ProjectStatus (`App\Enums\ProjectStatus`)

```php
enum ProjectStatus: string
{
    case Pending = 'pending';       // En attente
    case InProgress = 'in_progress'; // En cours
    case Completed = 'completed';    // Terminé
    case Blocked = 'blocked';        // Bloqué

    public static function labels(): array
    {
        return [
            self::Pending->value => 'En attente',
            self::InProgress->value => 'En cours',
            self::Completed->value => 'Terminé',
            self::Blocked->value => 'Bloqué',
        ];
    }
}
```

**Usage** : cast automatique dans Model, sélection dans formulaires Filament via `ProjectStatus::labels()`.

### 6.2 TaskStatus (`App\Enums\TaskStatus`)

```php
enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Blocked = 'blocked';

    public static function labels(): array { /* ... */ }
}
```

### 6.3 TaskPriority (`App\Enums\TaskPriority`)

```php
enum TaskPriority: string
{
    case Low = 'low';       // Basse
    case Medium = 'medium'; // Moyenne
    case High = 'high';     // Haute

    public static function labels(): array { /* ... */ }
}
```

### 6.4 UserRole (`App\Enums\UserRole`)

```php
enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Member = 'member';

    public static function labels(): array { /* ... */ }
}
```

**Avantages des enums** :
- Type-safe (pas de chaînes magiques).
- Autocomplete IDE.
- Validation native Laravel.
- Cohérence base/code.

---

## 7. Migrations & schéma

### 7.1 Migration `create_projects_table` (2025_12_19_091134)

```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->timestamps();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('status')->default('pending');
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
});
```

**Clé étrangère** : `user_id` → `users.id` avec cascade delete.

### 7.2 Migration `create_tasks_table` (2025_12_19_093250)

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->foreignId('project_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->text('description')->nullable();
    $table->string('priority')->default('medium');
    $table->string('status')->default('pending');
    $table->date('due_date')->nullable();
    $table->timestamps();
});
```

### 7.3 Migration `add_tracking_fields` (2025_12_24_120000)

Ajoute les champs de suivi avancé :

```php
Schema::table('projects', function (Blueprint $table) {
    $table->unsignedTinyInteger('progress')->default(0)->after('status');
    $table->decimal('risk_score', 5, 2)->nullable()->after('progress');
    $table->timestamp('archived_at')->nullable()->after('end_date');
    $table->softDeletes();
});

Schema::table('tasks', function (Blueprint $table) {
    $table->date('start_date')->nullable()->after('status');
    $table->timestamp('completed_at')->nullable()->after('due_date');
    $table->timestamp('archived_at')->nullable()->after('completed_at');
    $table->unsignedInteger('estimate_minutes')->nullable()->after('archived_at');
    $table->unsignedInteger('actual_minutes')->nullable()->after('estimate_minutes');
    $table->softDeletes();
});
```

**Soft deletes** : `deleted_at` permet rollback et audit.

### 7.4 Migration `add_role_to_users` (2025_12_24_131000)

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('role')->default('member')->after('password');
});
```

**Indexes recommandés** (à ajouter en prod) :
```php
$table->index(['status', 'archived_at']); // pour filtres rapides
$table->index('due_date'); // pour overdue queries
```

---

## 8. Models Eloquent

### 8.1 Model `Project`

**Fichier** : `app/Models/Project.php`

```php
namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'status', 'start_date', 'end_date',
        'user_id', 'archived_at', 'progress', 'risk_score',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'archived_at' => 'datetime',
        'progress' => 'integer',
        'risk_score' => 'float',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at')
            ->where('status', '!=', ProjectStatus::Completed);
    }

    public function scopeAtRisk($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('risk_score')->where('risk_score', '>', 0)
              ->orWhere(function ($inner) {
                  $inner->whereNotNull('end_date')
                        ->whereDate('end_date', '<', now());
              });
        });
    }
}
```

**Scopes métier** :
- `active()` : projets non archivés et non terminés.
- `atRisk()` : projets avec score de risque ou deadline dépassée.

### 8.2 Model `Task`

**Fichier** : `app/Models/Task.php`

```php
namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'project_id', 'user_id', 'description', 'priority',
        'status', 'start_date', 'due_date', 'completed_at', 'archived_at',
        'estimate_minutes', 'actual_minutes',
    ];

    protected $casts = [
        'priority' => TaskPriority::class,
        'status' => TaskStatus::class,
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
        'estimate_minutes' => 'integer',
        'actual_minutes' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }
}
```

**Scopes métier** :
- `overdue()` : tâches en retard (due_date < today et non completed).
- `active()` : tâches non archivées.

### 8.3 Model `User`

**Fichier** : `app/Models/User.php`

```php
namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isMember(): bool
    {
        return $this->role === UserRole::Member;
    }
}
```

---

## 9. Policies & autorisations (RBAC)

### 9.1 ProjectPolicy

**Fichier** : `app/Policies/ProjectPolicy.php`

```php
namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // Admin bypass all
    public function before(User $user, string $ability): bool|null
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isManager() || $user->isMember();
    }

    public function view(User $user, Project $project): bool
    {
        return $user->isManager() || $project->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->isManager() || $project->user_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->isManager() || $project->user_id === $user->id;
    }
}
```

**Logique** :
- **Admin** : bypass via `before()`.
- **Manager** : accès complet.
- **Member** : uniquement ses propres projets.

### 9.2 TaskPolicy

**Fichier** : `app/Policies/TaskPolicy.php`

```php
namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true; // tous peuvent lister
    }

    public function view(User $user, Task $task): bool
    {
        return $user->isManager()
            || $task->user_id === $user->id
            || $task->project?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isManager() || $task->project?->user_id === $user->id;
    }
}
```

**Logique tâches** :
- Membre peut voir/éditer les tâches qui lui sont assignées ou dans ses projets.
- Manager/Admin contrôle total.

### 9.3 Enregistrement des policies

**Fichier** : `app/Providers/AppServiceProvider.php`

```php
use Illuminate\Support\Facades\Gate;
use App\Models\{Project, Task};
use App\Policies\{ProjectPolicy, TaskPolicy};

public function boot(): void
{
    Gate::policy(Project::class, ProjectPolicy::class);
    Gate::policy(Task::class, TaskPolicy::class);
}
```

---

## 10. API REST

### 10.1 Routes (`routes/api.php`)

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{ProjectController, TaskController};

Route::middleware('auth')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('tasks', TaskController::class);
});
```

**Middleware `auth`** : exige authentification (Sanctum/Passport/session).

### 10.2 Endpoints disponibles

#### Projets

| Méthode | URL | Action | Description |
|---------|-----|--------|-------------|
| GET | `/api/projects` | index | Liste paginée avec filtres (status, search) |
| POST | `/api/projects` | store | Création projet (validation via StoreProjectRequest) |
| GET | `/api/projects/{id}` | show | Détails projet + relations (user, tasks) |
| PUT/PATCH | `/api/projects/{id}` | update | Mise à jour (validation via UpdateProjectRequest) |
| DELETE | `/api/projects/{id}` | destroy | Soft delete |

#### Tâches

| Méthode | URL | Action | Description |
|---------|-----|--------|-------------|
| GET | `/api/tasks` | index | Liste paginée avec filtres (status, priority, project_id, user_id, search) |
| POST | `/api/tasks` | store | Création tâche |
| GET | `/api/tasks/{id}` | show | Détails tâche + relations |
| PUT/PATCH | `/api/tasks/{id}` | update | Mise à jour |
| DELETE | `/api/tasks/{id}` | destroy | Soft delete |

### 10.3 Exemple de requêtes

#### Lister les projets (avec filtres)
```bash
GET /api/projects?status=in_progress&search=mobile
Authorization: Bearer {token}
```

**Réponse** :
```json
{
  "data": [
    {
      "id": 1,
      "name": "Application Mobile",
      "description": "Développement app iOS/Android",
      "status": "in_progress",
      "progress": 45,
      "risk_score": 12.5,
      "start_date": "2025-01-01",
      "end_date": "2025-06-30",
      "archived_at": null,
      "owner": {
        "id": 1,
        "name": "Admin Demo",
        "email": "admin@example.com"
      },
      "tasks_count": 12,
      "created_at": "2025-01-01T10:00:00Z",
      "updated_at": "2025-01-15T14:30:00Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

#### Créer un projet
```bash
POST /api/projects
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Nouveau site web",
  "description": "Refonte complète du site vitrine",
  "status": "pending",
  "start_date": "2025-02-01",
  "end_date": "2025-04-30",
  "user_id": 1,
  "progress": 0
}
```

**Réponse 201** : objet projet créé.

### 10.4 Form Requests

#### StoreProjectRequest

```php
namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // policy gère l'authz
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(ProjectStatus::class)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'user_id' => ['required', 'exists:users,id'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'risk_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'archived_at' => ['nullable', 'date'],
        ];
    }
}
```

**Validation stricte** :
- Enum via `Rules\Enum`.
- Dates cohérentes (`end_date >= start_date`).
- Foreign key existence.

---

## 11. Administration Filament

### 11.1 Structure Resource

Les ressources Filament sont organisées par entité (Projects, Tasks, Users) avec séparation des préoccupations :
- **Resource.php** : déclaration de la ressource, navigation, pages.
- **Schemas/** : formulaires et infolists réutilisables.
- **Tables/** : configuration des tables.
- **Pages/** : pages CRUD (List, Create, Edit, View).

### 11.2 Exemple ProjectResource

**Fichier** : `app/Filament/Resources/Projects/ProjectResource.php`

```php
namespace App\Filament\Resources\Projects;

use App\Models\Project;
use Filament\Resources\Resource;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationLabel = 'Projets';
    protected static ?string $navigationGroup = 'Gestion des projets';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
```

### 11.3 Formulaire dynamique

Formulaires avec validation en temps réel, relations, enums, dates localisées.

### 11.4 Tables avancées

- Colonnes triables/recherchables.
- Filtres par enum (SelectFilter).
- Actions inline (view, edit).
- Bulk actions (delete).
- Hover effects via thème custom.

---

## 12. Design & thème UI

### 12.1 Glassmorphism & dégradés

Thème sombre ultra-moderne avec effets de verre, dégradés radiaux, blur, animations subtiles.

**Fichier** : `resources/css/app.css`

**Caractéristiques** :
- Fond gradient multi-cercle (bleu/blanc translucide).
- Sidebar/topbar : backdrop-filter blur + transparence.
- Cartes : glassmorphism (rgba + blur).
- Boutons primaires : dégradé linéaire + glow.
- Hover tables : translateY + bg color shift.

### 12.2 Build assets

```bash
npm install
npm run build   # production
npm run dev     # développement avec watch
```

---

## 13. Factories & seeders

### 13.1 Factories

Génération réaliste de données avec Faker : dates cohérentes, enums random, relations.

### 13.2 Seeder démo

**Commande** : `php artisan db:seed`

Crée :
- 1 admin (`admin@example.com` / `password`)
- 3 projets
- 15 tâches (5 par projet)

---

## 14. Installation & configuration

### 14.1 Prérequis

- PHP ^8.2
- Composer 2.x
- Node.js >= 18
- MySQL 8 / PostgreSQL 14 / SQLite (dev)

### 14.2 Installation complète

```bash
# Cloner
git clone https://github.com/monja119/TaskFlow.git
cd TaskFlow

# Installer
composer install
npm install

# Config
cp .env.example .env
php artisan key:generate

# DB (éditer .env d'abord)
php artisan migrate --seed

# Assets
npm run build

# Lancer
php artisan serve
```

**URL admin** : `http://localhost:8000/admin`
**Login** : `admin@example.com` / `password`

### 14.3 Config Sanctum (recommandé)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Modifier `routes/api.php` :
```php
Route::middleware('auth:sanctum')->group(function () {
    // ...
});
```

---

## 15. Tests & qualité

### 15.1 Tests feature (à implémenter)

**Exemple** : `tests/Feature/ProjectApiTest.php`

```php
public function test_admin_can_list_projects()
{
    $admin = User::factory()->create(['role' => 'admin']);
    Project::factory()->count(3)->create();

    $response = $this->actingAs($admin)->getJson('/api/projects');

    $response->assertOk()->assertJsonCount(3, 'data');
}
```

### 15.2 Code style (Pint)

```bash
./vendor/bin/pint
```

---

## 16. Roadmap & fonctionnalités avancées

### Phase 2
- Dashboard widgets (KPIs).
- Activity log (audit).
- Notifications email/Slack.
- Tests E2E (Dusk).

### Phase 3 (IA)
- Scoring automatique de risque.
- Suggestions priorité ML.
- Estimation temps intelligente.
- Alertes proactives projets à risque.

### Phase 4
- Mobile app (React Native).
- Webhooks.
- Gantt/timeline.
- Rapports PDF/Excel.

---

## 17. Troubleshooting

### Erreur 419 (CSRF)
**Solution** : exclure `/api/*` du middleware CSRF ou utiliser Sanctum tokens.

### Erreur 401 API
**Solution** : configurer `auth:sanctum` ou vérifier token.

### Styles non appliqués
**Solution** :
```bash
npm run build
php artisan filament:clear
php artisan optimize:clear
```

### Enum non reconnu
**Solution** : vérifier PHP >= 8.1, valeurs en base = enum values.

### Performance tables
**Solution** : ajouter indexes :
```php
$table->index(['status', 'due_date']);
```

---

## Conclusion

Documentation technique exhaustive couvrant architecture, code, API, admin Filament, design, installation et troubleshooting.

**Support** : [GitHub Issues](https://github.com/monja119/TaskFlow/issues)
