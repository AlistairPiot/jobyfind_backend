# Contraintes de Validation des Entités - Jobyfind Backend

Ce document décrit toutes les contraintes de validation ajoutées aux entités du backend Jobyfind.

## Vue d'ensemble

Toutes les entités ont été enrichies avec des assertions de validation Symfony utilisant les composants :

-   `Symfony\Component\Validator\Constraints` pour les validations de base
-   `Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity` pour les contraintes d'unicité

## Entités et Validations

### 1. User (Utilisateur)

#### Contraintes sur l'entité

-   **UniqueEntity** : L'email doit être unique dans la base de données

#### Propriétés validées

##### email

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Email` : Format email valide
-   `@Assert\Length(max: 180)` : Maximum 180 caractères

##### password

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(min: 8, max: 255)` : Entre 8 et 255 caractères

##### roles

-   `@Assert\NotNull` : Obligatoire
-   `@Assert\All` + `@Assert\Choice` : Seuls les rôles ROLE_USER, ROLE_COMPANY, ROLE_SCHOOL, ROLE_ADMIN sont autorisés

##### locationCity/locationRegion

-   `@Assert\Length(max: 255)` : Maximum 255 caractères

##### locationCode

-   `@Assert\Length(max: 255)` : Maximum 255 caractères
-   `@Assert\Regex(/^\d{5}$/)` : Doit contenir exactement 5 chiffres

##### firstName/lastName

-   `@Assert\Length(min: 2, max: 255)` : Entre 2 et 255 caractères
-   `@Assert\Regex` : Lettres, espaces, apostrophes et tirets uniquement

##### nameCompany/nameSchool

-   `@Assert\Length(max: 255)` : Maximum 255 caractères

##### contactEmail

-   `@Assert\Email` : Format email valide si fourni
-   `@Assert\Length(max: 255)` : Maximum 255 caractères

##### description

-   `@Assert\Length(max: 2000)` : Maximum 2000 caractères

---

### 2. Mission

#### Propriétés validées

##### name

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(min: 3, max: 255)` : Entre 3 et 255 caractères

##### user (créateur)

-   `@Assert\NotNull` : Utilisateur créateur obligatoire

##### type (contrat)

-   `@Assert\NotNull` : Type de contrat obligatoire

##### description

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(min: 10, max: 5000)` : Entre 10 et 5000 caractères

---

### 3. JobApplication (Candidature)

#### Propriétés validées

##### status

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Choice` : PENDING, ACCEPTED, REJECTED, CANCELLED uniquement

##### DateApplied

-   `@Assert\NotNull` : Date obligatoire
-   `@Assert\Type` : Doit être une DateTimeImmutable

##### user

-   `@Assert\NotNull` : Utilisateur candidat obligatoire

##### missions

-   `@Assert\Count(min: 1)` : Au moins une mission associée

---

### 4. Skill (Compétence)

#### Contraintes sur l'entité

-   **UniqueEntity** : Le nom doit être unique

#### Propriétés validées

##### name

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(min: 2, max: 255)` : Entre 2 et 255 caractères
-   `@Assert\Regex` : Lettres, chiffres, espaces et caractères spéciaux (. + # - \_)

---

### 5. Type (Type de contrat)

#### Contraintes sur l'entité

-   **UniqueEntity** : Le nom doit être unique

#### Propriétés validées

##### name

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(min: 2, max: 255)` : Entre 2 et 255 caractères
-   `@Assert\Regex` : Lettres, chiffres, espaces, tirets, underscores et slashes

---

### 6. RequestBadge (Demande de badge)

#### Propriétés validées

##### requestDate

-   `@Assert\NotNull` : Date obligatoire
-   `@Assert\Type` : Doit être une DateTimeImmutable

##### responseDate

-   `@Assert\Type` : Doit être une DateTimeImmutable si fournie
-   `@Assert\Expression` : Ne peut pas être antérieure à requestDate

##### status

-   `@Assert\Choice` : PENDING, APPROVED, REJECTED uniquement

##### user

-   `@Assert\NotNull` : Utilisateur demandeur obligatoire

##### school

-   `@Assert\NotNull` : École obligatoire
-   `@Assert\Expression` : L'école ne peut pas être le même utilisateur que le demandeur

---

### 7. SkillCategory (Catégorie de compétences)

#### Contraintes sur l'entité

-   **UniqueEntity** : Le nom doit être unique

#### Propriétés validées

##### name

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(min: 2, max: 255)` : Entre 2 et 255 caractères
-   `@Assert\Regex` : Lettres, chiffres, espaces et caractères spéciaux (. - \_)

##### description

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(min: 10, max: 1000)` : Entre 10 et 1000 caractères

---

### 8. Media (Fichier média)

#### Propriétés validées

##### uploadAt

-   `@Assert\NotNull` : Date obligatoire
-   `@Assert\Type` : Doit être une DateTimeInterface

##### path

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(max: 255)` : Maximum 255 caractères
-   `@Assert\Regex` : Caractères autorisés pour un chemin de fichier

##### name

-   `@Assert\NotBlank` : Obligatoire
-   `@Assert\Length(min: 1, max: 255)` : Entre 1 et 255 caractères
-   `@Assert\Regex` : Caractères autorisés pour un nom de fichier

##### user

-   `@Assert\NotNull` : Utilisateur propriétaire obligatoire

---

### 9. MissionRecommendation (Recommandation de mission)

#### Contraintes sur l'entité

-   **UniqueEntity** : Combinaison mission + student + school doit être unique

#### Propriétés validées

##### mission

-   `@Assert\NotNull` : Mission obligatoire

##### student

-   `@Assert\NotNull` : Étudiant obligatoire

##### school

-   `@Assert\NotNull` : École obligatoire
-   `@Assert\Expression` : L'école ne peut pas être le même utilisateur que l'étudiant

##### recommendedAt

-   `@Assert\NotNull` : Date obligatoire
-   `@Assert\Type` : Doit être une DateTimeImmutable

---

## Avantages des Validations

### 1. Sécurité

-   Prévention de l'injection de données malveillantes
-   Validation des formats (email, dates, etc.)
-   Contrôle des longueurs pour éviter les attaques par débordement

### 2. Intégrité des données

-   Garantit la cohérence des données en base
-   Évite les doublons avec UniqueEntity
-   Vérifie les relations obligatoires

### 3. Expérience utilisateur

-   Messages d'erreur clairs en français
-   Validation côté serveur pour la sécurité
-   Feedback immédiat sur les erreurs de saisie

### 4. Maintenance

-   Validations centralisées dans les entités
-   Documentation claire des contraintes
-   Facilité de modification des règles

---

## Utilisation

### Validation automatique

Les validations sont automatiquement appliquées par API Platform lors des opérations CRUD.

### Validation manuelle

```php
use Symfony\Component\Validator\Validator\ValidatorInterface;

$validator = $this->get(ValidatorInterface::class);
$violations = $validator->validate($entity);

if (count($violations) > 0) {
    // Traiter les erreurs
    foreach ($violations as $violation) {
        echo $violation->getMessage();
    }
}
```

### Messages d'erreur personnalisés

Tous les messages sont en français et expliquent clairement le problème rencontré.

---

## Bonnes pratiques

1. **Validation côté client ET serveur** : Ne jamais faire confiance uniquement à la validation côté client
2. **Messages explicites** : Utiliser des messages d'erreur clairs pour l'utilisateur
3. **Contraintes logiques** : Utiliser `@Assert\Expression` pour les validations complexes
4. **Performance** : Les validations sont optimisées pour ne pas impacter les performances
5. **Tests** : Toutes les contraintes doivent être testées avec des cas de test appropriés

---

_Ces validations garantissent la robustesse et la fiabilité de l'application Jobyfind._
