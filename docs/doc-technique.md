# TaskFlow — Documentation technique

## Vue d'ensemble
TaskFlow est une application Laravel 12 avec Filament v4 pour la console d'administration. API REST sécurisée par policies, modèles Eloquent enrichis (projets/tâches), enums métier et thèmes personnalisés.

## Pile & dépendances clés
- PHP ^8.2, Laravel 12
- Filament 4 (panel admin)
- Policies Laravel (RBAC simple via `UserRole` enum)
- Tailwind v4 (via `@import 'tailwindcss'`) + thème custom dans `resources/css/app.css`

## Architecture
- Domain: enums `App\Enums\ProjectStatus`, `TaskStatus`, `TaskPriority`, `UserRole`.
- Données: migrations projets/tâches + migration d'extension (progression, risk_score, archived, soft deletes), rôle sur users.
- Application: contrôleurs API `ProjectController`, `TaskController` + FormRequests + Resources JSON.
- Admin: Filament Resources pour Projects/Tasks/Users (tables, formulaires, infolists), thème glassmorphism.

## Modèle de données (champs principaux)
- `projects`: `name`, `description?`, `status` (enum), `start_date?`, `end_date?`, `progress` (0-100), `risk_score?`, `archived_at?`, `user_id` (owner), timestamps, soft deletes.
- `tasks`: `title`, `description?`, `priority` (enum), `status` (enum), `start_date?`, `due_date?`, `completed_at?`, `archived_at?`, `estimate_minutes?`, `actual_minutes?`, `project_id`, `user_id`, timestamps, soft deletes.
- `users`: `name`, `email`, `password`, `role` (enum `admin|manager|member`), timestamps.

## Règles métiers & scopes
- `Project::scopeActive()`, `scopeAtRisk()`.
- `Task::scopeOverdue()`, `scopeActive()`.
- Casts d’enum sur status/priority/role pour cohérence.

## Sécurité & autorisations
- Policies: `ProjectPolicy`, `TaskPolicy` (admin bypass, manager accès large, member restreint à ses projets/tâches).
- `authorizeResource` sur contrôleurs API + middleware `auth` sur routes API.
- (À faire) Ajouter Sanctum/Passport pour tokens API, ou session guard pour Filament.

## API REST
- Routes: `routes/api.php` protégées `auth`.
- `GET /api/projects`: filtres `status`, `search`; pagination.
- `POST /api/projects`: création (FormRequest `StoreProjectRequest`).
- `GET /api/projects/{project}`: détails + relations.
- `PUT/PATCH /api/projects/{project}`: mise à jour.
- `DELETE /api/projects/{project}`: soft delete.
- `GET /api/tasks`: filtres `status`, `priority`, `project_id`, `user_id`, `search`; pagination.
- `POST /api/tasks`, `GET/PUT/PATCH/DELETE /api/tasks/{task}` idem logique.
- Resources JSON: `ProjectResource`, `TaskResource`, `UserResource`.

## Admin Filament
- Resources: `app/Filament/Resources/Projects/*`, `Tasks/*`, `Users/*`.
- Tables: filtres status/priority, actions view/edit, bulk delete.
- Formulaires: enums, dates, estimation, progression, risk_score.
- Thème: `resources/css/app.css` (fond gradient, glass, boutons dégradés).

## Données de démo
- `DatabaseSeeder`: utilisateur admin `admin@example.com` / mdp `password`; 3 projets chacun avec 5 tâches.
- Factories pour users (role member par défaut), projects, tasks.

## Commandes utiles
- Installer deps: `composer install && npm install`
- Build front: `npm run build` (ou `npm run dev` en local)
- Migrations: `php artisan migrate`
- Seed: `php artisan db:seed`
- Filament assets clear (si styles): `php artisan filament:clear`

## Personnalisation rapide
- Couleurs thème: ajuster variables dans `resources/css/app.css` (palette brand, fond, glow).
- Rôles/policies: affiner `UserRole`, policies, et middleware.
- Widgets dashboard: ajouter dans Filament (non encore créés) pour KPIs projets/tâches.

## Tests (à ajouter)
- Feature: APIs projets/tâches (authz, validation, scopes).
- Filament: tests Livewire/Panel pour formulaires et actions.
- Unit: services futurs (scoring risque, suggestions).

## Points d'attention
- `auth` middleware nécessite un guard configuré (Sanctum recommandé) avant usage API.
- Soft deletes: penser aux purges/archives.
- Statuts/enum doivent correspondre aux valeurs en base.
