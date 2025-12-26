# TaskFlow

Plateforme moderne de gestion de projets et de tâches construite avec Laravel et Filament.

## Fonctionnalités

- Gestion complète des projets et des tâches
- Interface d'administration intuitive avec Filament
- API REST pour intégrations externes
- Système de rôles et permissions
- Notifications en temps réel
- Design moderne et responsive

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL 8.0 ou supérieur
- Node.js et npm
- Redis (optionnel, pour le cache et les files d'attente)

## Installation rapide

Pour une installation rapide avec Docker, consultez le guide [QUICKSTART.md](QUICKSTART.md).

## Installation manuelle

1. Cloner le dépôt :
```bash
git clone https://github.com/monja119/TaskFlow.git
cd TaskFlow
```

2. Installer les dépendances :
```bash
composer install
npm install
```

3. Configurer l'environnement :
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer la base de données dans `.env` et lancer les migrations :
```bash
php artisan migrate --seed
```

5. Construire les assets :
```bash
npm run build
```

6. Lancer le serveur de développement :
```bash
php artisan serve
```

## Documentation

- [Guide de démarrage rapide](QUICKSTART.md)
- [Documentation technique](docs/doc-technique.md)
- [Documentation utilisateur](docs/doc-utilisateur.md)
- [Documentation API](docs/api-documentation.md)
- [Guide de déploiement](deploy/README.md)

## Tests

```bash
php artisan test
```

## Déploiement

Consultez le dossier [deploy/](deploy/) pour les instructions de déploiement avec Docker.

## Licence

Ce projet est sous licence propriétaire. Tous droits réservés.

## Support

Pour toute question ou problème, veuillez ouvrir une issue sur GitHub.
