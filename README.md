# Backend Projet Jobyfind

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