# TaskFlow — Documentation utilisateur

> Organisez. Priorisez. Avancez intelligemment.

---

## Table des matières

1. [Introduction & bienvenue](#1-introduction--bienvenue)
2. [Premiers pas](#2-premiers-pas)
3. [Gestion des projets](#3-gestion-des-projets)
4. [Gestion des tâches](#4-gestion-des-tâches)
5. [Rôles & permissions](#5-rôles--permissions)
6. [Interface d'administration (Filament)](#6-interface-dadministration-filament)
7. [Utilisation de l'API](#7-utilisation-de-lapi)
8. [Workflows recommandés](#8-workflows-recommandés)
9. [Résolution des problèmes courants](#9-résolution-des-problèmes-courants)
10. [Conseils & bonnes pratiques](#10-conseils--bonnes-pratiques)

---

## 1. Introduction & bienvenue

### Qu'est-ce que TaskFlow ?

**TaskFlow** est une plateforme moderne de gestion de projets et de tâches conçue pour les équipes, freelances et managers. Elle permet de :
- Créer et suivre des projets avec statuts, dates, progression et score de risque.
- Organiser les tâches par priorité, statut, échéance et assignation.
- Gérer les utilisateurs avec des rôles (Admin, Manager, Membre).
- Accéder à une interface d'administration élégante (Filament).
- Utiliser une API REST pour intégrations externes (mobile, automation).

### À qui s'adresse TaskFlow ?

- **Managers de projet** : suivre l'avancement, identifier les risques, planifier.
- **Développeurs / designers** : visualiser leurs tâches, mettre à jour le statut.
- **Admins IT** : gérer les utilisateurs, accès complet.
- **Freelances** : organiser leur portefeuille de projets.

### Fonctionnalités principales

1. **Projets** : CRUD, statuts (En attente, En cours, Terminé, Bloqué), progression 0-100%, score de risque, archivage.
2. **Tâches** : CRUD, priorités (Basse, Moyenne, Haute), statuts, dates début/échéance, estimation/temps réel, assignation utilisateur.
3. **Utilisateurs** : rôles Admin/Manager/Membre, permissions granulaires.
4. **Admin Filament** : interface visuelle avec tableaux, formulaires, filtres, actions groupées.
5. **API REST** : endpoints sécurisés pour intégrations (mobile, webhooks, etc.).
6. **Design ultra-moderne** : glassmorphism, dégradés, animations fluides.

---

## 2. Premiers pas

### 2.1 Accès à la plateforme

**URL** : `https://votre-domaine.com/admin` (en production) ou `http://localhost:8000/admin` (en local).

**Identifiants de démo** :
- **Email** : `admin@example.com`
- **Mot de passe** : `password`

> ⚠️ **Important** : Changez immédiatement le mot de passe de l'admin après la première connexion.

### 2.2 Interface d'accueil

Après connexion, vous arrivez sur le **Dashboard Filament** :
- **Sidebar gauche** : navigation (Projets, Tâches, Utilisateurs).
- **Topbar** : compte utilisateur, notifications (future), thème sombre/clair (par défaut sombre).
- **Contenu central** : liste des ressources (tableaux) ou formulaires.

### 2.3 Changer son mot de passe

1. Cliquer sur l'avatar en haut à droite.
2. Sélectionner **Profil**.
3. Modifier le mot de passe (minimum 8 caractères).
4. Enregistrer.

---

## 3. Gestion des projets

### 3.1 Créer un projet

1. Dans la sidebar, cliquer sur **Projets** → **Créer un projet** (bouton vert en haut à droite).
2. Remplir le formulaire :
   - **Nom** : nom du projet (ex: "Application Mobile").
   - **Description** : contexte, objectifs (optionnel mais recommandé).
   - **Statut** : sélectionner parmi :
     - **En attente** : projet planifié mais pas démarré.
     - **En cours** : en développement actif.
     - **Terminé** : objectif atteint.
     - **Bloqué** : problème bloquant.
   - **Date début / Date fin** : planification.
   - **Propriétaire** : sélectionner l'utilisateur responsable (Manager ou Admin).
   - **Progression (%)** : 0-100 (ajustable manuellement).
   - **Score de risque** : 0-100 (optionnel, calcul manuel ou automatique dans phase future).
   - **Archivé le** : laisser vide pour projet actif.

3. Cliquer sur **Créer**.

**Résultat** : Le projet apparaît dans la liste avec badge de statut coloré.

### 3.2 Consulter un projet

1. Dans la liste des projets, cliquer sur le nom ou l'icône **œil** (view).
2. L'**Infolist** affiche :
   - Nom, description, statut (badge).
   - Dates début/fin.
   - Propriétaire (lien vers fiche utilisateur).
   - Progression (barre visuelle).
   - Score de risque (badge alerte si > 30).
   - Nombre de tâches liées.
   - Dates création/modification.

### 3.3 Modifier un projet

1. Dans la liste, cliquer sur l'icône **crayon** (edit) ou sur la fiche projet → **Modifier**.
2. Ajuster les champs nécessaires.
3. Enregistrer.

**Cas d'usage** :
- Mettre à jour la progression après avancement.
- Changer le statut (ex: "En cours" → "Terminé").
- Ajuster les dates si retard/avance.

### 3.4 Archiver un projet

1. Éditer le projet.
2. Renseigner la date dans **Archivé le** (ou sélectionner "Aujourd'hui").
3. Enregistrer.

**Effet** : Le projet disparaît des vues par défaut (filtre "actif" actif). Pour voir les projets archivés, désactiver le filtre "Archivé = Non" dans la liste.

### 3.5 Supprimer un projet

1. Dans la liste, cocher la case du projet.
2. Cliquer sur **Actions groupées** → **Supprimer** (ou icône poubelle si action inline).
3. Confirmer.

**Soft delete** : Le projet est marqué `deleted_at` (suppression logique). Possibilité de restaurer via base de données si besoin.

### 3.6 Filtrer les projets

**Filtres disponibles** :
- **Statut** : En attente / En cours / Terminé / Bloqué.
- **Propriétaire** : par utilisateur.
- **Archivé** : Oui / Non.
- **À risque** : projets avec score de risque > 0 ou deadline dépassée.

**Recherche** : barre de recherche globale (nom, description).

**Tri** : cliquer sur les en-têtes de colonnes (Nom, Statut, Progression, Dates).

---

## 4. Gestion des tâches

### 4.1 Créer une tâche

1. Aller dans **Tâches** → **Créer une tâche**.
2. Remplir :
   - **Titre** : action précise (ex: "Développer l'API utilisateurs").
   - **Projet** : sélectionner le projet parent (obligatoire).
   - **Assigné à** : utilisateur responsable (obligatoire).
   - **Description** : détails, critères d'acceptation.
   - **Priorité** : Basse / Moyenne / Haute (défaut: Moyenne).
   - **Statut** : En attente / En cours / Terminé / Bloqué (défaut: En attente).
   - **Date début** : quand commencer.
   - **Date d'échéance** : deadline.
   - **Temps estimé (minutes)** : estimation.
   - **Temps réel (minutes)** : à remplir après complétion.
   - **Archivé le** : laisser vide pour tâche active.

3. **Créer**.

**Résultat** : Tâche affichée dans la liste avec badge priorité et statut.

### 4.2 Consulter une tâche

1. Cliquer sur le titre ou icône **œil**.
2. Voir :
   - Titre, projet (lien), assigné à (lien).
   - Priorité (badge coloré : rouge=haute, orange=moyenne, vert=basse).
   - Statut (badge).
   - Dates début/échéance, complété le.
   - Estimation vs temps réel.
   - Description complète.

### 4.3 Mettre à jour le statut d'une tâche

**Workflow typique** :
1. Tâche créée → statut **En attente**.
2. Utilisateur commence → éditer → statut **En cours**.
3. Tâche terminée → statut **Terminé** + renseigner **Complété le** (date automatique recommandée).
4. Si blocage → statut **Bloqué** + décrire le problème dans description.

**Astuce** : utiliser les actions rapides dans la table (si configuré : bouton "Marquer terminé").

### 4.4 Filtrer les tâches

**Filtres** :
- **Statut** : En attente / En cours / Terminé / Bloqué.
- **Priorité** : Basse / Moyenne / Haute.
- **Projet** : par projet.
- **Assigné à** : par utilisateur.
- **En retard** : tâches avec échéance < aujourd'hui et non terminées.
- **Archivé** : Oui / Non.

**Recherche** : titre, description.

### 4.5 Tâches en retard (alerte)

Les tâches avec **échéance dépassée** et statut != Terminé apparaissent en rouge (ou badge "En retard" dans la table).

**Action recommandée** :
- Prioriser ces tâches.
- Si impossible, repousser l'échéance ou bloquer avec justification.

### 4.6 Archiver / supprimer une tâche

Idem projet : renseigner "Archivé le" ou supprimer (soft delete).

---

## 5. Rôles & permissions

### 5.1 Les 3 rôles

| Rôle | Permissions | Cas d'usage |
|------|-------------|-------------|
| **Admin** | Accès complet (bypass toutes les policies), gestion utilisateurs, configuration plateforme | Administrateur système |
| **Manager** | Création/modification/suppression projets et tâches, accès tous les projets | Chef de projet, Scrum Master |
| **Membre** | Consultation tous les projets, modification uniquement de ses projets/tâches assignées | Développeur, Designer, Contributeur |

### 5.2 Détails des permissions

#### Admin
- **Projets** : CRUD sans restriction.
- **Tâches** : CRUD sans restriction.
- **Utilisateurs** : création, édition, suppression, changement de rôle.
- **Configuration** : accès settings Filament (future).

#### Manager
- **Projets** : création illimitée, modification/suppression tous les projets.
- **Tâches** : création illimitée, modification/suppression toutes les tâches.
- **Utilisateurs** : lecture seule (sauf si permission ajoutée).

#### Membre
- **Projets** : lecture de tous, modification uniquement des projets dont il est propriétaire (`user_id`).
- **Tâches** : lecture de toutes, modification uniquement des tâches qui lui sont assignées (`user_id`) ou dans ses projets.
- **Utilisateurs** : pas d'accès.

### 5.3 Changer le rôle d'un utilisateur (Admin uniquement)

1. Aller dans **Utilisateurs**.
2. Éditer l'utilisateur.
3. Sélectionner le nouveau rôle.
4. Enregistrer.

**Effet immédiat** : Les permissions sont appliquées dès la prochaine requête.

---

## 6. Interface d'administration (Filament)

### 6.1 Navigation

**Sidebar** :
- **Dashboard** : vue d'ensemble (widgets KPI à venir).
- **Projets** : liste des projets.
- **Tâches** : liste des tâches.
- **Utilisateurs** : gestion des comptes (Admin/Manager).

**Groupes** : Les ressources sont organisées par groupe ("Gestion des projets", "Administration").

### 6.2 Tableaux (Index)

**Colonnes affichées** :
- **Projets** : Nom, Statut, Propriétaire, Progression, Score de risque, Dates.
- **Tâches** : Titre, Projet, Assigné à, Priorité, Statut, Échéance.

**Actions** :
- **Icône œil** : consulter.
- **Icône crayon** : éditer.
- **Icône poubelle** : supprimer (inline ou bulk).

**Pagination** : 10/25/50/100 résultats par page (configurable en bas de table).

**Tri** : cliquer sur en-tête colonne (flèche haut/bas).

**Recherche globale** : barre en haut du tableau (recherche nom/description).

### 6.3 Formulaires (Create/Edit)

**Sections** :
- **Informations principales** : champs obligatoires (nom, statut, etc.).
- **Dates & Planning** : date début, fin, échéance.
- **Tracking** : progression, temps, risque.

**Validation temps réel** : erreurs affichées sous les champs (ex: "La date de fin doit être après la date de début").

**Enregistrement** : bouton vert **Créer** ou **Enregistrer**.

### 6.4 Infolists (View)

Vue détaillée en lecture seule avec :
- Badges colorés pour statuts/priorités.
- Barre de progression visuelle.
- Liens vers relations (propriétaire, projet).
- Dates formatées.

**Actions disponibles** : Éditer, Supprimer (boutons en haut).

### 6.5 Filtres

**Activer les filtres** : icône entonnoir en haut à droite du tableau.

**Filtres disponibles** :
- SelectFilter (statut, priorité, rôle).
- TernaryFilter (Oui/Non/Tous pour Archivé).
- DateFilter (plage de dates).

**Réinitialiser** : bouton "Réinitialiser les filtres".

### 6.6 Actions groupées (Bulk actions)

1. Cocher les cases des lignes à traiter.
2. Sélectionner l'action : **Supprimer**, **Archiver**, etc.
3. Confirmer.

**Limite** : certaines actions peuvent être restreintes par rôle.

---

## 7. Utilisation de l'API

### 7.1 Authentification

L'API utilise **Laravel Sanctum** (ou session auth en dev).

**Obtenir un token (Sanctum)** :

```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Réponse** :
```json
{
  "token": "1|abc123def456..."
}
```

**Utiliser le token** :
```bash
Authorization: Bearer 1|abc123def456...
```

### 7.2 Endpoints disponibles

#### Projets

**Lister les projets** :
```bash
GET /api/projects
Authorization: Bearer {token}
```

**Paramètres query** :
- `status` : filtrer par statut (ex: `in_progress`).
- `search` : recherche nom/description.
- `page` : pagination.

**Réponse** :
```json
{
  "data": [
    {
      "id": 1,
      "name": "Application Mobile",
      "description": "...",
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
      "tasks_count": 12
    }
  ],
  "links": { ... },
  "meta": { "current_page": 1, "total": 25, ... }
}
```

**Créer un projet** :
```bash
POST /api/projects
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Nouveau site web",
  "description": "Refonte complète",
  "status": "pending",
  "start_date": "2025-02-01",
  "end_date": "2025-04-30",
  "user_id": 1,
  "progress": 0
}
```

**Réponse 201** : objet projet créé.

**Consulter un projet** :
```bash
GET /api/projects/{id}
Authorization: Bearer {token}
```

**Réponse** : objet projet avec relations (owner, tasks).

**Modifier un projet** :
```bash
PUT /api/projects/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "progress": 60,
  "status": "in_progress"
}
```

**Supprimer un projet** :
```bash
DELETE /api/projects/{id}
Authorization: Bearer {token}
```

**Réponse 204** : suppression réussie.

#### Tâches

**Lister les tâches** :
```bash
GET /api/tasks
Authorization: Bearer {token}
```

**Paramètres query** :
- `status` : filtrer par statut.
- `priority` : filtrer par priorité.
- `project_id` : tâches d'un projet.
- `user_id` : tâches d'un utilisateur.
- `search` : recherche titre/description.

**Créer une tâche** :
```bash
POST /api/tasks
Content-Type: application/json
Authorization: Bearer {token}

{
  "title": "Développer API",
  "project_id": 1,
  "user_id": 2,
  "priority": "high",
  "status": "pending",
  "due_date": "2025-02-15",
  "estimate_minutes": 480
}
```

**Modifier une tâche** :
```bash
PUT /api/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "completed",
  "completed_at": "2025-01-20T15:30:00Z",
  "actual_minutes": 520
}
```

**Supprimer une tâche** :
```bash
DELETE /api/tasks/{id}
Authorization: Bearer {token}
```

### 7.3 Erreurs courantes

| Code | Signification | Solution |
|------|---------------|----------|
| 401 Unauthorized | Token manquant/invalide | Vérifier Authorization header |
| 403 Forbidden | Permissions insuffisantes | Vérifier rôle utilisateur |
| 404 Not Found | Ressource introuvable | Vérifier l'ID |
| 422 Unprocessable Entity | Validation échouée | Voir détails dans `errors` JSON |
| 500 Internal Server Error | Erreur serveur | Contacter admin |

**Exemple erreur 422** :
```json
{
  "message": "Les données fournies sont invalides.",
  "errors": {
    "end_date": ["La date de fin doit être après ou égale à la date de début."],
    "status": ["Le statut sélectionné n'est pas valide."]
  }
}
```

---

## 8. Workflows recommandés

### 8.1 Démarrer un nouveau projet

1. **Créer le projet** : définir nom, description, dates, propriétaire.
2. **Statut initial** : "En attente".
3. **Décomposer en tâches** :
   - Identifier les grandes étapes.
   - Créer 1 tâche par étape (titre précis, assignation, priorité, échéance).
4. **Passer le projet en "En cours"** dès début effectif.
5. **Mettre à jour la progression** régulièrement (hebdomadaire recommandé).
6. **Suivre les tâches** : marquer complétées au fur et à mesure.
7. **Archiver** quand terminé ou abandonné.

### 8.2 Prioriser les tâches

**Méthode Eisenhower** :
- **Urgente + Importante** → Priorité Haute, échéance courte.
- **Non urgente + Importante** → Priorité Moyenne, planifier.
- **Urgente + Non importante** → Déléguer si possible.
- **Non urgente + Non importante** → Priorité Basse ou supprimer.

**Utiliser les filtres** :
- Afficher "Priorité Haute" + "En retard" → traiter en premier.

### 8.3 Réunion de suivi (stand-up quotidien)

1. Chaque membre consulte ses tâches "En cours".
2. Mettre à jour le statut (terminé, bloqué, etc.).
3. Identifier les blocages → passer en statut "Bloqué" + description.
4. Manager vérifie les tâches en retard → reprioriser ou repousser.

### 8.4 Reporting de progression

**Hebdomadaire** :
1. Filtrer projets "En cours".
2. Vérifier progression (%) vs planning.
3. Identifier projets avec score de risque > 30 → actions correctives.
4. Exporter rapport (feature future : PDF/Excel).

---

## 9. Résolution des problèmes courants

### 9.1 Je ne peux pas créer de projet

**Causes possibles** :
- Rôle = Membre → seuls Admin/Manager peuvent créer.
- **Solution** : demander à un Admin de changer votre rôle.

### 9.2 Je ne vois pas certains projets

**Causes** :
- Projets archivés → activer filtre "Archivé = Oui".
- Projets supprimés (soft delete) → contacter Admin pour restauration.
- **Solution** : désactiver les filtres, vérifier recherche.

### 9.3 Erreur "Les données fournies sont invalides"

**Causes** :
- Enum invalide (ex: status = "test" au lieu de "pending").
- Dates incohérentes (end_date < start_date).
- Champ requis manquant.

**Solution** : vérifier les valeurs autorisées :
- Statuts projets : `pending`, `in_progress`, `completed`, `blocked`.
- Statuts tâches : idem.
- Priorités : `low`, `medium`, `high`.

### 9.4 La progression ne se met pas à jour automatiquement

**Comportement actuel** : la progression est manuelle (0-100%).

**Feature future** : calcul automatique basé sur % de tâches complétées.

**Solution temporaire** : mettre à jour manuellement après complétion des tâches.

### 9.5 Les styles ne s'affichent pas correctement

**Solutions** :
1. Vider cache navigateur (Ctrl+Shift+R).
2. Vérifier que les assets sont compilés : `npm run build`.
3. Vider cache Laravel : `php artisan optimize:clear`.
4. Si local : relancer `npm run dev`.

### 9.6 API retourne 401 Unauthorized

**Causes** :
- Token expiré ou invalide.
- Middleware auth non configuré.

**Solutions** :
- Régénérer token via `/api/login`.
- Vérifier header `Authorization: Bearer {token}`.
- Si session auth (dev), vérifier cookies.

---

## 10. Conseils & bonnes pratiques

### 10.1 Organisation

- **1 projet = 1 objectif clair** : éviter les projets "fourre-tout".
- **Tâches atomiques** : 1 tâche = 1 action précise (ex: "Développer endpoint /api/users" plutôt que "Faire l'API").
- **Assigner systématiquement** : chaque tâche doit avoir un responsable.
- **Dates réalistes** : mieux vaut rallonger qu'être en retard.

### 10.2 Communication

- **Description détaillée** : inclure contexte, critères d'acceptation, liens vers specs.
- **Commentaires** (feature future) : pour discussions dans une tâche.
- **Statut "Bloqué"** : toujours expliquer le blocage et taguer responsable.

### 10.3 Suivi

- **Mettre à jour quotidiennement** : statut des tâches en cours.
- **Progression projet** : mise à jour hebdomadaire minimum.
- **Score de risque** : réévaluer chaque semaine (future : automatique via IA).

### 10.4 Sécurité

- **Mots de passe forts** : minimum 12 caractères, mixte majuscules/minuscules/chiffres/symboles.
- **Rôles appropriés** : ne pas donner Admin à tout le monde.
- **API tokens** : régénérer périodiquement, ne jamais commit dans Git.
- **HTTPS** : obligatoire en production.

### 10.5 Performance

- **Archiver régulièrement** : projets/tâches terminés → archiver pour alléger les listes.
- **Supprimer avec parcimonie** : préférer archiver (audit trail).
- **Filtres** : utiliser les filtres plutôt que scroller.

---

## Conclusion

Cette documentation couvre l'utilisation complète de TaskFlow pour tous les rôles. Pour toute question ou bug, consulter :
- **Documentation technique** : `docs/doc-technique.md`
- **Issues GitHub** : [https://github.com/monja119/TaskFlow/issues](https://github.com/monja119/TaskFlow/issues)
- **Support** : contacter l'administrateur système.

**Bon travail avec TaskFlow !** 🚀
