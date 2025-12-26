# TaskFlow - Guide de déploiement

Ce répertoire contient tous les fichiers nécessaires pour déployer l'application TaskFlow Laravel Filament en utilisant Docker et Docker Compose.

## Structure

```
deploy/
├── docker-compose.yml    # Configuration complète de Docker Compose
├── Dockerfile           # Construction Docker multi-étapes pour Laravel
├── entrypoint.sh        # Script d'initialisation du conteneur
├── .env.example         # Modèle de variables d'environnement
├── deploy.sh           # Script de déploiement automatisé
├── nginx/              # Configuration Nginx (optionnel)
├── mysql/              # Configuration personnalisée MySQL
└── README.md           # Ce fichier
```

## Démarrage rapide

### 1. Configuration de l'environnement

```bash
# Copier le fichier d'environnement exemple
cp .env.example .env

# Éditer le fichier .env avec vos valeurs
nano .env
```

**Important** : Configurez au moins ces variables requises :
- `APP_KEY` (exécutez `php artisan key:generate` pour générer)
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `REDIS_PASSWORD`
- Configuration mail si vous utilisez les notifications

### 2. Déploiement pour le développement local

```bash
# Construire et déployer localement
./deploy.sh deploy-build

# Ou déployer en utilisant des images pré-construites depuis le registre
./deploy.sh deploy
```

### 3. Vérifier le déploiement

```bash
# Vérifier le statut des services
./deploy.sh status

# Afficher tous les logs
./deploy.sh logs

# Afficher les logs d'un service spécifique
./deploy.sh logs app
```

## Services déployés

Une fois déployés, les services sont accessibles à :

- **Application (Filament)** : http://localhost
- **Base de données MySQL** : localhost:3306
- **Cache Redis** : localhost:6379

## Script de déploiement (deploy.sh)

Le script `deploy.sh` fournit une gestion complète du déploiement :

### Commandes principales

```bash
./deploy.sh deploy          # Déployer avec les images du registre
./deploy.sh deploy-build    # Construire localement et déployer
./deploy.sh start           # Démarrer les services
./deploy.sh stop            # Arrêter les services
./deploy.sh restart         # Redémarrer les services
```

### Commandes de surveillance

```bash
./deploy.sh status          # Afficher le statut des services
./deploy.sh logs            # Afficher tous les logs
./deploy.sh logs app        # Afficher les logs de l'application uniquement
./deploy.sh health          # Exécuter la vérification de santé
```

### Commandes de maintenance

```bash
./deploy.sh build           # Construire les images Docker
./deploy.sh validate        # Valider la configuration
./deploy.sh cleanup         # Nettoyer toutes les ressources
./deploy.sh backup-db       # Sauvegarder la base de données
./deploy.sh restore-db <file> # Restaurer la base de données
./deploy.sh update          # Mettre à jour l'application
```

### Commandes Artisan

```bash
./deploy.sh artisan migrate              # Exécuter les migrations
./deploy.sh artisan db:seed              # Initialiser la base de données
./deploy.sh artisan cache:clear          # Vider le cache
./deploy.sh artisan queue:work           # Démarrer le worker de file d'attente
./deploy.sh artisan make:filament-user   # Créer un utilisateur Filament
```

### Accès au conteneur

```bash
# Ouvrir bash dans le conteneur app
./deploy.sh exec app bash

# Exécuter une commande dans un conteneur spécifique
./deploy.sh exec app php artisan inspire
```

## Architecture

### Services

#### 1. **db** (MySQL 8.0)
- Base de données principale
- Volumes persistants
- Vérifications de santé configurées
- Ressources : 512M RAM, 0.5 CPU

#### 2. **redis** (Redis 7)
- Stockage de cache et de session
- Backend de file d'attente
- Vérifications de santé configurées
- Ressources : 128M RAM, 0.25 CPU

#### 3. **app** (Laravel/Filament)
- Application principale
- Dépend de db et redis
- Migration automatique au démarrage
- Ressources : 1G RAM, 1 CPU

#### 4. **nginx** (Optionnel)
- Proxy inverse avec terminaison SSL
- Activer avec `--profile with-nginx`
- Ressources : 128M RAM, 0.25 CPU

#### 5. **queue** (Optionnel)
- Worker de file d'attente dédié
- Activer avec `--profile with-queue`
- Ressources : 512M RAM, 0.5 CPU

#### 6. **scheduler** (Optionnel)
- Planificateur de tâches Laravel
- Activer avec `--profile with-scheduler`
- Ressources : 256M RAM, 0.25 CPU

### Volumes

- `db_data` : Données persistantes MySQL
- `redis_data` : Données persistantes Redis
- `app_storage` : Stockage de l'application
- `app_logs` : Logs de l'application

### Réseau

- Réseau bridge personnalisé : `taskflow-network`
- Sous-réseau : 172.28.0.0/16

## Configuration

### Variables d'environnement

#### Paramètres de l'application
```env
APP_NAME=TaskFlow
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=http://localhost
```

#### Configuration de la base de données
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=taskflow_user
DB_PASSWORD=mot_de_passe_sécurisé
```

#### Cache et file d'attente
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

#### Options de déploiement
```env
WAIT_FOR_DB=true
RUN_MIGRATIONS=true
RUN_SEEDERS=false
QUEUE_WORKER=true
SCHEDULER=true
```

#### Configuration initiale
```env
CREATE_ADMIN=true
ADMIN_NAME=Admin
ADMIN_EMAIL=admin@taskflow.com
ADMIN_PASSWORD=mot_de_passe_sécurisé
```

## Production Deployment

### Using GitHub Actions

The CI/CD workflow automatically:
1. Validates Docker and Docker Compose files
2. Builds and pushes images to GitHub Container Registry
3. Deploys to production server

Triggers:
- Push to `dev` or `main` branch
- Manual trigger via GitHub Actions UI

### Manual Production Deployment

```bash
# On production server
cd /opt/taskflow/deploy

# Ensure environment is configured
source .env

# Deploy
./deploy.sh deploy

# Verify
./deploy.sh health
./deploy.sh status
```

### Initial Production Setup

1. **Clone repository**:
```bash
git clone https://github.com/monja119/TaskFlow.git
cd TaskFlow/deploy
```

2. **Configure environment**:
```bash
cp .env.example .env
nano .env
```

3. **Set admin creation**:
```bash
# In .env file
CREATE_ADMIN=true
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD=your_secure_password
```

4. **Deploy**:
```bash
./deploy.sh deploy-build
```

5. **Disable admin creation** (after first deployment):
```bash
# In .env file
CREATE_ADMIN=false
```

## Bonnes pratiques de sécurité

### Gestion des secrets

1. **Ne jamais commiter le fichier `.env`**
2. **Utiliser des mots de passe forts** :
```bash
# Générer un mot de passe sécurisé
openssl rand -base64 32
```

3. **Configurer les secrets GitHub** pour CI/CD :
   - `APP_KEY`
   - `DB_PASSWORD`
   - `REDIS_PASSWORD`
   - Identifiants mail
   - Clés SSH pour le déploiement

### Configuration SSL/TLS

1. **Activer le profil nginx** :
```bash
./deploy.sh start --profile with-nginx
```

2. **Placer les certificats SSL** dans `deploy/nginx/ssl/`

3. **Mettre à jour la configuration nginx** pour HTTPS

## Surveillance

### Vérifications de santé

Vérifications de santé intégrées pour :
- MySQL : `mysqladmin ping`
- Redis : `redis-cli ping`
- Application : `curl http://localhost/health`

Vérifier la santé :
```bash
./deploy.sh health
docker compose ps
```

### Gestion des logs

```bash
# Suivre tous les logs
./deploy.sh logs

# Logs d'un service spécifique
./deploy.sh logs app
./deploy.sh logs db
./deploy.sh logs redis

# Dernières 100 lignes
docker compose logs --tail=100

# Rechercher dans les logs
./deploy.sh logs app | grep ERROR
```

### Surveillance des performances

```bash
# Statistiques des conteneurs
docker stats

# Utilisation des ressources
docker compose ps --format json | jq

# Vérifier l'utilisation du disque
docker system df
```

## Sauvegarde et restauration

### Sauvegarde de la base de données

```bash
# Créer une sauvegarde
./deploy.sh backup-db

# Les sauvegardes sont stockées dans deploy/backups/
ls -lh deploy/backups/
```

### Restauration de la base de données

```bash
# Restaurer depuis une sauvegarde
./deploy.sh restore-db deploy/backups/db_backup_20241226_120000.sql
```

### Sauvegardes automatisées

Ajouter au crontab pour des sauvegardes quotidiennes automatisées :
```bash
0 2 * * * cd /opt/taskflow/deploy && ./deploy.sh backup-db >> /var/log/taskflow-backup.log 2>&1
```

## Troubleshooting

### Services Won't Start

```bash
# Check logs
./deploy.sh logs

# Validate configuration
./deploy.sh validate

# Check service status
./deploy.sh status
```

### Database Connection Issues

```bash
# Vérifier les logs de la base de données
./deploy.sh logs db

# Tester la connexion
./deploy.sh exec app php artisan db:show

# Réinitialiser la base de données
./deploy.sh stop
docker volume rm deploy_db_data
./deploy.sh start
```

### Erreurs de l'application

```bash
# Vérifier les logs de l'application
./deploy.sh logs app

# Vider le cache
./deploy.sh artisan cache:clear
./deploy.sh artisan config:clear

# Vérifier les permissions
./deploy.sh exec app ls -la storage/
```

### Conflits de ports

```bash
# Vérifier l'utilisation des ports
netstat -tulpn | grep -E ":(80|3306|6379)"

# Changer les ports dans .env
APP_PORT=8080
DB_PORT=3307
REDIS_PORT=6380

# Redémarrer les services
./deploy.sh restart
```

### Problèmes de performance

```bash
# Vérifier l'utilisation des ressources
docker stats

# Optimiser Laravel
./deploy.sh artisan optimize
./deploy.sh artisan config:cache
./deploy.sh artisan route:cache
./deploy.sh artisan view:cache

# Redémarrer les services
./deploy.sh restart
```

### Réinitialisation complète

```bash
# Option nucléaire - supprime tout
./deploy.sh cleanup

# Puis redéployer
./deploy.sh deploy-build
```

## Mises à jour et maintenance

### Mises à jour de l'application

```bash
# Récupérer le dernier code
git pull origin main

# Mettre à jour et migrer
./deploy.sh update
```

### Mises à jour des dépendances

```bash
# Mettre à jour les dépendances Composer
./deploy.sh exec app composer update

# Mettre à jour les dépendances npm
./deploy.sh exec app npm update

# Reconstruire les images
./deploy.sh build
./deploy.sh restart
```

### Maintenance système

```bash
# Nettoyer les ressources inutilisées
docker system prune -af

# Vérifier l'espace disque
df -h
docker system df

# Rotation des logs
./deploy.sh artisan log:clear
```

## Fichiers de configuration supplémentaires

### Configuration Nginx

Créer `deploy/nginx/default.conf` :
```nginx
server {
    listen 80;
    server_name _;
    
    location / {
        proxy_pass http://app:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### Configuration MySQL

Créer `deploy/mysql/my.cnf` :
```ini
[mysqld]
max_connections = 100
innodb_buffer_pool_size = 256M
query_cache_size = 32M
```

### Configuration Supervisor

Créer `deploy/supervisord.conf` :
```ini
[supervisord]
nodaemon=true

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true

[program:php-fpm]
command=php-fpm -F
autostart=true
autorestart=true
```

## Ressources supplémentaires

- [Documentation Laravel](https://laravel.com/docs)
- [Documentation Filament](https://filamentphp.com/docs)
- [Documentation Docker](https://docs.docker.com)
- [Documentation Docker Compose](https://docs.docker.com/compose)

## Support

Pour les problèmes et questions :
1. Consultez cette documentation
2. Vérifiez les logs : `./deploy.sh logs`
3. Exécutez la vérification de santé : `./deploy.sh health`
4. Créez une issue sur GitHub

## Licence

Ce projet suit la même licence que l'application principale TaskFlow.
