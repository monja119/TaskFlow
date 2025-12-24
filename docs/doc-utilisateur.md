# TaskFlow — Guide utilisateur (Admin/Manager/Member)

## Accès
- Admin panel Filament : `https://votre-domaine.tld/admin` (ou URL configurée). Auth requise.
- API : endpoints sous `/api` (auth requise, prévoir Sanctum/Passport ou session guard).

## Rôles
- **Admin** : plein accès (bypass policies).
- **Manager** : gère tous les projets/tâches, crée/édite/supprime.
- **Membre** : voit ses projets/tâches et ceux dont il est owner ; création limitée.

## Connexion
1) Ouvrir l’URL admin.
2) Se connecter avec vos identifiants. Demo: `admin@example.com` / `password` (à changer en prod).

## Navigation Filament
- Barre latérale : sections Projets, Tâches, Utilisateurs.
- Thème sombre glassmorphism : cartes et tableaux translucides, boutons bleus dégradés.
- Barre haute : actions globales, profil, déconnexion.

## Gestion des projets
- **Lister** : menu Projets → table avec filtres par statut.
- **Créer** : bouton “Créer” → renseigner nom, description, statut, progression, risque, dates, propriétaire.
- **Voir** : action “Voir” pour consulter les détails.
- **Éditer** : action “Éditer” pour mettre à jour infos/avancement.
- **Supprimer/Archiver** : action suppression (soft delete). Archivage via champ `archived_at` (si exposé) ou suppression.

## Gestion des tâches
- **Lister** : menu Tâches → filtres statut, priorité, en retard.
- **Créer** : bouton “Créer” → saisir titre, description, projet, assigné, priorité, statut, dates, estimation.
- **Voir/Éditer** : actions dédiées dans la table.
- **Supprimer** : soft delete.

## Gestion des utilisateurs
- Menu Utilisateurs : créer/éditer utilisateurs et rôle (`admin`, `manager`, `member`).

## API rapide
- Auth requise (`auth` middleware). Prévoir token (Sanctum) ou cookie de session.
- Projets
  - `GET /api/projects?status=pending&search=mot`
  - `POST /api/projects` (name, status, user_id, dates…)
  - `PATCH /api/projects/{id}`
  - `DELETE /api/projects/{id}`
- Tâches
  - `GET /api/tasks?status=pending&priority=high&project_id=1`
  - `POST /api/tasks` (title, status, priority, project_id, user_id, dates, estimation)
  - `PATCH /api/tasks/{id}`
  - `DELETE /api/tasks/{id}`

## Données de démo
- Seed : `php artisan db:seed` crée l’admin et 3 projets + 15 tâches.

## Personnaliser l’apparence
- Modifier les variables dans `resources/css/app.css` (couleurs brand, arrière-plan, effets glass). Rebuild : `npm run build`.

## Bonnes pratiques
- Mettre à jour le mot de passe admin immédiatement.
- Configurer Sanctum/Passport avant d’exposer l’API.
- Sauvegardes régulières de la base et des assets.
- Activer HTTPS et les en-têtes de sécurité (HSTS, CSP si possible).

## Support
- Logs : `storage/logs/laravel.log`
- Cache/filament : `php artisan filament:clear`
- En cas d’erreur 419/401 API, vérifier l’auth guard et les tokens.
