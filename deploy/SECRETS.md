# Configuration des secrets GitHub

Ce document liste tous les secrets et variables GitHub requis pour le pipeline CI/CD.

## Secrets requis

Configurez-les dans : **Settings → Secrets and Variables → Actions → Secrets**

### Secrets de l'application

| Nom du secret | Description | Exemple / Comment générer |
|------------|-------------|-------------------------|
| `APP_KEY` | Clé de chiffrement de l'application Laravel | Exécuter : `php artisan key:generate --show` |
| `DB_PASSWORD` | Mot de passe de la base de données MySQL | Mot de passe aléatoire fort (min 16 caractères) |
| `REDIS_PASSWORD` | Mot de passe du cache Redis | Mot de passe aléatoire fort (min 16 caractères) |

### Configuration mail

| Nom du secret | Description | Exemple |
|------------|-------------|---------|
| `MAIL_HOST` | Nom d'hôte du serveur SMTP | smtp.mailtrap.io |
| `MAIL_PORT` | Port du serveur SMTP | 2525 |
| `MAIL_USERNAME` | Nom d'utilisateur SMTP | votre-nom-utilisateur |
| `MAIL_PASSWORD` | Mot de passe SMTP | votre-mot-de-passe |

### Secrets de déploiement

| Nom du secret | Description | Comment générer |
|------------|-------------|-----------------|
| `SSH_PRIVATE_KEY` | Clé privée SSH pour l'accès au serveur | Générer : `ssh-keygen -t ed25519 -C "github-actions"` |
| `SERVER_HOST` | IP/nom d'hôte du serveur de production | 192.168.1.100 ou server.example.com |
| `SERVER_USER` | Nom d'utilisateur SSH pour le serveur | ubuntu, deploy, etc. |

### Secrets optionnels

| Nom du secret | Description | Par défaut |
|------------|-------------|---------|
| `GITHUB_TOKEN` | Fourni automatiquement par GitHub Actions | - |

## Variables requises

Configurez-les dans : **Settings → Secrets and Variables → Actions → Variables**

### Variables de l'application

| Nom de la variable | Description | Exemple |
|--------------|-------------|---------|
| `APP_NAME` | Nom de l'application | TaskFlow |
| `APP_URL` | URL de l'application | https://taskflow.example.com |
| `DB_DATABASE` | Nom de la base de données | taskflow |
| `DB_USERNAME` | Nom d'utilisateur de la base de données | taskflow_user |

## Instructions de configuration

### 1. Générer une paire de clés SSH

```bash
# Générer une paire de clés SSH
ssh-keygen -t ed25519 -C "github-actions-deploy" -f github_deploy_key

# Copier la clé publique sur le serveur
ssh-copy-id -i github_deploy_key.pub user@server-ip

# Ajouter la clé privée aux secrets GitHub
cat github_deploy_key
# Copier toute la sortie incluant -----BEGIN/END OPENSSH PRIVATE KEY-----
```

### 2. Générer les secrets de l'application

```bash
# Générer APP_KEY
cd /chemin/vers/TaskFlow
php artisan key:generate --show

# Générer des mots de passe sécurisés
openssl rand -base64 32  # Pour DB_PASSWORD
openssl rand -base64 32  # Pour REDIS_PASSWORD
```

### 3. Configurer les secrets GitHub

1. Allez sur votre dépôt sur GitHub
2. Naviguez vers **Settings → Secrets and variables → Actions**
3. Cliquez sur **New repository secret**
4. Ajoutez chaque secret du tableau ci-dessus
5. Passez à l'onglet **Variables** et ajoutez les variables

### 4. Vérifier la configuration

Après avoir ajouté tous les secrets, vous pouvez tester le workflow :

1. Allez dans l'onglet **Actions**
2. Sélectionnez **Deploy TaskFlow Application**
3. Cliquez sur **Run workflow**
4. Choisissez l'environnement (staging/production)
5. Surveillez la progression du déploiement

## Bonnes pratiques de sécurité

### Rotation des secrets

Faire tourner les secrets régulièrement :
- Clés SSH : Tous les 6 mois
- Mots de passe de base de données : Tous les 3 mois
- Tokens API : Tous les mois

### Contrôle d'accès

1. **Limiter l'accès aux secrets** :
   - Seuls les administrateurs du dépôt devraient accéder aux secrets
   - Utiliser les règles de protection d'environnement

2. **Utiliser des secrets spécifiques à l'environnement** :
   ```yaml
   # Dans votre workflow
   environment:
     name: production
   ```

3. **Activer les journaux d'audit** :
   - Surveiller l'accès aux secrets dans Settings → Audit log

### Stockage des secrets

Ne jamais commiter les secrets dans le dépôt :
- Ajouter à `.gitignore` : `.env`, `*.key`, `*.pem`
- Utiliser `.env.example` pour la structure uniquement
- Documenter les secrets requis sans les valeurs

## Résolution des problèmes

### Problèmes de connexion SSH

```bash
# Tester la connexion SSH depuis la machine locale
ssh -i github_deploy_key user@server-ip

# Vérifier le format de la clé SSH
head -1 github_deploy_key
# Devrait afficher : -----BEGIN OPENSSH PRIVATE KEY-----
```

### Erreurs de secret non trouvé

1. Vérifier que le nom du secret correspond exactement (sensible à la casse)
2. Vérifier si le secret est défini pour le bon environnement
3. S'assurer que le workflow a la permission d'accéder aux secrets

### Échec du déploiement

1. Vérifier les logs du workflow dans l'onglet Actions
2. Vérifier que tous les secrets requis sont définis
3. Tester l'accès SSH manuellement
4. Vérifier les logs du serveur : `/var/log/syslog`

## Configuration spécifique à l'environnement

### Environnement de staging

```yaml
# .github/workflows/deploy.yml
environment:
  name: staging
  url: https://staging.taskflow.example.com
```

Secrets requis (préfixer avec `STAGING_`) :
- `STAGING_SERVER_HOST`
- `STAGING_DB_PASSWORD`
- etc.

### Environnement de production

```yaml
# .github/workflows/deploy.yml
environment:
  name: production
  url: https://taskflow.example.com
```

Secrets requis :
- Tous les secrets listés ci-dessus
- Règles de protection supplémentaires recommandées

## Ressources supplémentaires

- [Documentation des secrets GitHub Actions](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
- [Bonnes pratiques de déploiement Laravel](https://laravel.com/docs/deployment)
- [Bonnes pratiques de sécurité Docker](https://docs.docker.com/develop/security-best-practices/)

## Support

Pour les problèmes de configuration des secrets :
1. Vérifier les logs GitHub Actions
2. Consulter cette documentation
3. Contacter les administrateurs du dépôt
