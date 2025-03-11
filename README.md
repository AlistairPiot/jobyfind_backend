# Backend Projet Jobyfind

![Symfony](https://img.shields.io/badge/Symfony-6.4-black?style=for-the-badge&logo=symfony)
![API Platform](https://img.shields.io/badge/API%20Platform-3.1-blue?style=for-the-badge&logo=api)
![Doctrine](https://img.shields.io/badge/Doctrine-3.3-orange?style=for-the-badge)



### Lancement du serveur :
```bash
  symfony server:start
```

## Package
### Apache pack :
```bash
  composer require symfony/apache-pack 
```
### Doctrine package :
```bash
  composer require symfony/orm-pack
```

### Maker package :
```bash
  composer require symfony/maker-bundle --dev
```

### API package :
```bash
  composer require api
```

## Database :

### Database creation :
```bash
  symfony console doctrine:database:create
```

### Creation migration :
```bash
  symfony console make:migration
```

### Migration execution :
```bash
  symfony console doctrine:migrations:migrate
```