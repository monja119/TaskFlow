# TaskFlow - Guide de démarrage rapide

Démarrez TaskFlow en 5 minutes !

## Prérequis

- Docker Desktop ou Docker Engine (20.10+)
- Docker Compose (2.0+)
- Git
- 4 Go de RAM minimum
- 10 Go d'espace disque

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/monja119/TaskFlow.git
cd TaskFlow
```

### 2. Configurer l'environnement

```bash
# Copier le modèle d'environnement
cp deploy/.env.example deploy/.env

# Éditer la configuration
nano deploy/.env
```

**Modifications minimales requises :**

```env
# Générer avec : php artisan key:generate --show
APP_KEY=base64:votre-clé-générée-ici

# Identifiants de la base de données
DB_DATABASE=taskflow
DB_USERNAME=taskflow_user
DB_PASSWORD=changez-ce-mot-de-passe-sécurisé

# Mot de passe Redis
REDIS_PASSWORD=changez-ce-mot-de-passe-redis

# Premier utilisateur administrateur
CREATE_ADMIN=true
ADMIN_EMAIL=admin@taskflow.com
ADMIN_PASSWORD=changez-ce-mot-de-passe-admin
```

### 3. Déployer

```bash
# Option A : Utiliser Makefile (recommandé)
make deploy-build

# Option B : Utiliser le script de déploiement
cd deploy
./deploy.sh deploy-build

# Option C : Utiliser Docker Compose directement
cd deploy
docker compose up -d
```

### 4. Vérifier l'installation

```bash
# Vérifier le statut des services
make status

# Ou
./deploy/deploy.sh status

# Afficher les logs
make logs
```

## Accéder à l'application

Une fois déployé, accédez à :

- **Application** : http://localhost
- **Connexion** : Utilisez les identifiants du fichier `.env`
  - Email : Valeur de `ADMIN_EMAIL`
  - Mot de passe : Valeur de `ADMIN_PASSWORD`

## Commandes courantes

### Utiliser Makefile (le plus simple)

```bash
make help              # Afficher toutes les commandes
make start             # Démarrer les services
make stop              # Arrêter les services
make restart           # Redémarrer les services
make logs              # Afficher les logs
make logs-app          # Afficher les logs de l'application uniquement
make shell             # Ouvrir un shell dans le conteneur
make artisan CMD="..." # Exécuter une commande artisan
make migrate           # Exécuter les migrations
make backup            # Sauvegarder la base de données
```

### Utiliser le script de déploiement

```bash
./deploy/deploy.sh help           # Afficher toutes les commandes
./deploy/deploy.sh start          # Démarrer les services
./deploy/deploy.sh stop           # Arrêter les services
./deploy/deploy.sh logs           # Afficher les logs
./deploy/deploy.sh artisan migrate # Exécuter les migrations
```

### Docker Compose direct

```bash
cd deploy
docker compose ps              # Afficher les services
docker compose logs -f app     # Suivre les logs de l'application
docker compose exec app bash   # Accéder au shell
```

## Étapes suivantes

### 1. Créer des utilisateurs supplémentaires

```bash
# Utiliser Makefile
make filament-user

# Ou utiliser le script de déploiement
./deploy/deploy.sh artisan make:filament-user
```

### 2. Configurer les emails

Mettre à jour dans `deploy/.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre-nom-utilisateur
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@taskflow.com
```

Redémarrer les services :

```bash
make restart
```

### 3. Activer les services en arrière-plan

Mettre à jour dans `deploy/.env` :

```env
QUEUE_WORKER=true
SCHEDULER=true
```

Démarrer avec les profils :

```bash
cd deploy
docker compose --profile with-queue --profile with-scheduler up -d
```

### 4. Configurer SSL (Production)

1. Placer les certificats SSL dans `deploy/nginx/ssl/` :
   - `cert.pem` (certificat)
   - `key.pem` (clé privée)

2. Décommenter la section HTTPS dans `deploy/nginx/default.conf`

3. Redémarrer nginx :
   ```bash
   make restart
   ```

## Résolution des problèmes

### Les services ne démarrent pas

```bash
# Vérifier les logs
make logs

# Valider la configuration
make validate

# Vérifier Docker
docker ps
docker version
```

### Échec de connexion à la base de données

```bash
# Vérifier les logs de la base de données
make logs-db

# Réinitialiser la base de données
make stop
docker volume rm deploy_db_data
make start
```

### Port déjà utilisé

Mettre à jour les ports dans `deploy/.env` :

```env
APP_PORT=8080      # Changer de 80
DB_PORT=3307       # Changer de 3306
REDIS_PORT=6380    # Changer de 6379
```

### Permission refusée

```bash
# Rendre les scripts exécutables
chmod +x deploy/deploy.sh
chmod +x deploy/entrypoint.sh
```

### Impossible d'accéder à l'application

```bash
# Vérifier si les services sont en cours d'exécution
make status

# Vérifier la santé
make health

# Tester le point de terminaison
curl http://localhost/health
```

## Liste de vérification de sécurité

Avant de passer en production :

- [ ] Changer tous les mots de passe par défaut
- [ ] Générer une `APP_KEY` forte
- [ ] Configurer SSL/TLS
- [ ] Définir `APP_DEBUG=false`
- [ ] Définir `APP_ENV=production`
- [ ] Configurer les règles du pare-feu
- [ ] Mettre en place des sauvegardes régulières
- [ ] Activer la surveillance
- [ ] Vérifier les permissions des fichiers
- [ ] Configurer la limitation de débit

## Surveillance

### Vérifications de santé

```bash
# Santé de base
curl http://localhost/health

# Santé détaillée
curl http://localhost/health/detailed

# Vérification de disponibilité
curl http://localhost/health/ready
```

### Utilisation des ressources

```bash
# Statistiques des conteneurs
docker stats

# Ou utiliser make
make top
```

### Logs

```bash
# Tous les logs
make logs

# Service spécifique
make logs-app
make logs-db
make logs-redis

# Suivre les logs
make watch-logs
```

## Mises à jour

### Mettre à jour l'application

```bash
# Récupérer les dernières modifications
git pull origin main

# Mettre à jour les services
make update

# Ou manuellement
make build
make restart
make migrate
```

### Mettre à jour les dépendances

```bash
# Accéder au conteneur
make shell

# Mettre à jour les packages Composer
composer update

# Mettre à jour les packages npm
npm update

# Quitter et reconstruire
exit
make build
make restart
```

## Sauvegarde et restauration

### Sauvegarder la base de données

```bash
# Créer une sauvegarde
make backup

# Les sauvegardes sont stockées dans : deploy/backups/
```

### Restaurer la base de données

```bash
./deploy/deploy.sh restore-db deploy/backups/db_backup_20241226_120000.sql
```

### Sauvegardes automatisées

Ajouter au crontab :

```bash
# Ouvrir crontab
crontab -e

# Ajouter une sauvegarde quotidienne à 2h du matin
0 2 * * * cd /chemin/vers/TaskFlow && make backup >> /var/log/taskflow-backup.log 2>&1
```

## Obtenir de l'aide

1. **Documentation** : Consultez `deploy/README.md` pour des informations détaillées
2. **Logs** : Exécutez `make logs` pour voir ce qui se passe
3. **Vérification de santé** : Exécutez `make health` pour vérifier les services
4. **Issues GitHub** : Signalez des bugs ou demandez des fonctionnalités

## Ressources supplémentaires

- [Documentation complète](deploy/README.md)
- [Configuration des secrets](deploy/SECRETS.md)
- [Documentation Laravel](https://laravel.com/docs)
- [Documentation Filament](https://filamentphp.com/docs)
- [Documentation Docker](https://docs.docker.com)

## Vous êtes prêt !

Votre application TaskFlow est maintenant en cours d'exécution. Bonne gestion de projets !

Pour le déploiement en production, consultez [deploy/README.md](deploy/README.md).
