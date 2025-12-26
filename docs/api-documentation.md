# Documentation API REST - TaskFlow

## Authentification

L'API TaskFlow utilise **Laravel Sanctum** pour l'authentification par token. Tous les endpoints (sauf login et register) nécessitent un token Bearer.

### Format d'authentification
```
Authorization: Bearer {token}
```

## Rate Limiting

- **Login**: 10 requêtes par minute
- **Register**: 5 requêtes par minute  
- **Endpoints authentifiés**: 120 requêtes par minute

Les headers de réponse incluent:
- `X-RateLimit-Limit`: Limite de requêtes
- `X-RateLimit-Remaining`: Requêtes restantes

## Endpoints

### Authentification

#### POST /api/auth/register
Créer un nouveau compte utilisateur.

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "device_name": "My Device" // optionnel
}
```

**Response (201):**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

#### POST /api/auth/login
Se connecter et obtenir un token API.

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123",
  "device_name": "My Device" // optionnel
}
```

**Response (200):**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

**Error (422):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

#### GET /api/auth/user
Récupérer les informations de l'utilisateur authentifié.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "member",
    "created_at": "2025-12-26T10:00:00.000000Z"
  }
}
```

#### POST /api/auth/logout
Révoquer le token actuel.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "message": "Token revoked successfully"
}
```

---

### Gestion des Tokens

#### GET /api/tokens
Lister tous les tokens de l'utilisateur.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "tokens": [
    {
      "id": 1,
      "name": "My Device",
      "abilities": ["*"],
      "last_used_at": "2025-12-26T12:30:00",
      "created_at": "2025-12-26T10:00:00",
      "expires_at": null
    }
  ]
}
```

#### POST /api/tokens
Créer un nouveau token API.

**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "name": "Integration Token",
  "abilities": ["*"], // optionnel, par défaut ["*"]
  "expires_at": "2026-12-26T10:00:00" // optionnel
}
```

**Response (201):**
```json
{
  "token": "2|xyz789...",
  "token_info": {
    "id": 2,
    "name": "Integration Token",
    "abilities": ["*"],
    "expires_at": "2026-12-26T10:00:00"
  }
}
```

#### DELETE /api/tokens/{tokenId}
Révoquer un token spécifique.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "message": "Token revoked successfully"
}
```

**Error (404):**
```json
{
  "message": "Token not found"
}
```

#### DELETE /api/tokens
Révoquer tous les tokens sauf le token actuel.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "message": "All other tokens revoked successfully"
}
```

---

### Projets

Tous les endpoints nécessitent: `Authorization: Bearer {token}`

#### GET /api/projects
Lister les projets de l'utilisateur.

**Query Parameters:**
- `status`: Filtrer par statut (planned, in_progress, completed, on_hold, cancelled)
- `search`: Recherche dans le nom et la description
- `sort_by`: Champ de tri (created_at, updated_at, deadline, name)
- `sort_direction`: Direction du tri (asc, desc)

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Projet A",
      "description": "Description du projet",
      "status": "in_progress",
      "deadline": "2025-12-31",
      "user": {
        "id": 1,
        "name": "John Doe"
      },
      "created_at": "2025-12-26T10:00:00.000000Z"
    }
  ]
}
```

#### POST /api/projects
Créer un nouveau projet.

**Body:**
```json
{
  "name": "Nouveau Projet",
  "description": "Description du projet",
  "status": "planned",
  "deadline": "2025-12-31"
}
```

**Response (201):**
```json
{
  "data": {
    "id": 2,
    "name": "Nouveau Projet",
    "description": "Description du projet",
    "status": "planned",
    "deadline": "2025-12-31",
    "user": {...},
    "created_at": "2025-12-26T10:00:00.000000Z"
  }
}
```

#### GET /api/projects/{id}
Récupérer un projet spécifique avec ses tâches.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Projet A",
    "description": "Description",
    "status": "in_progress",
    "deadline": "2025-12-31",
    "user": {...},
    "tasks": [...]
  }
}
```

#### PUT /api/projects/{id}
Mettre à jour un projet.

**Body:**
```json
{
  "name": "Projet A - Modifié",
  "description": "Nouvelle description",
  "status": "completed",
  "deadline": "2026-01-15"
}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Projet A - Modifié",
    ...
  }
}
```

#### DELETE /api/projects/{id}
Supprimer un projet.

**Response (204):** No Content

---

### Tâches

Tous les endpoints nécessitent: `Authorization: Bearer {token}`

#### GET /api/tasks
Lister les tâches de l'utilisateur.

**Query Parameters:**
- `status`: Filtrer par statut (todo, in_progress, done, cancelled)
- `priority`: Filtrer par priorité (low, medium, high, urgent)
- `project_id`: Filtrer par projet
- `search`: Recherche dans le titre et la description
- `sort_by`: Champ de tri (created_at, updated_at, due_date, priority)
- `sort_direction`: Direction du tri (asc, desc)

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Tâche 1",
      "description": "Description",
      "status": "todo",
      "priority": "high",
      "due_date": "2025-12-30",
      "project": {...},
      "user": {...}
    }
  ]
}
```

#### POST /api/tasks
Créer une nouvelle tâche.

**Body:**
```json
{
  "title": "Nouvelle tâche",
  "description": "Description de la tâche",
  "status": "todo",
  "priority": "medium",
  "due_date": "2025-12-30",
  "project_id": 1
}
```

**Response (201):**
```json
{
  "data": {
    "id": 2,
    "title": "Nouvelle tâche",
    ...
  }
}
```

#### GET /api/tasks/{id}
Récupérer une tâche spécifique.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "title": "Tâche 1",
    "description": "Description",
    "status": "todo",
    "priority": "high",
    "due_date": "2025-12-30",
    "project": {...},
    "user": {...}
  }
}
```

#### PUT /api/tasks/{id}
Mettre à jour une tâche.

**Body:**
```json
{
  "title": "Tâche modifiée",
  "status": "in_progress",
  "priority": "urgent"
}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "title": "Tâche modifiée",
    ...
  }
}
```

#### DELETE /api/tasks/{id}
Supprimer une tâche.

**Response (204):** No Content

---

## Codes de Statut HTTP

- **200**: Succès
- **201**: Ressource créée
- **204**: Succès sans contenu
- **401**: Non authentifié
- **403**: Non autorisé (permissions insuffisantes)
- **404**: Ressource non trouvée
- **422**: Erreur de validation
- **429**: Trop de requêtes (rate limit dépassé)
- **500**: Erreur serveur

## Erreurs

Format standard des erreurs:
```json
{
  "message": "Message d'erreur principal",
  "errors": {
    "field_name": [
      "Détail de l'erreur"
    ]
  }
}
```

## Exemple d'utilisation

### Avec cURL

```bash
# Login
curl -X POST https://api.taskflow.example/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Récupérer les projets
curl -X GET https://api.taskflow.example/api/projects \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Accept: application/json"

# Créer une tâche
curl -X POST https://api.taskflow.example/api/tasks \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{"title":"Ma tâche","project_id":1,"status":"todo","priority":"high"}'
```

### Avec JavaScript (Fetch)

```javascript
// Login
const loginResponse = await fetch('https://api.taskflow.example/api/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'john@example.com',
    password: 'password123'
  })
});

const { token } = await loginResponse.json();

// Récupérer les projets
const projectsResponse = await fetch('https://api.taskflow.example/api/projects', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});

const projects = await projectsResponse.json();
```

## Sécurité

- Toujours utiliser HTTPS en production
- Les tokens ne sont affichés qu'une seule fois lors de leur création
- Révoquer les tokens non utilisés régulièrement
- Utiliser des tokens avec durée d'expiration pour les intégrations
- Ne jamais partager vos tokens dans le code source ou les commits Git
