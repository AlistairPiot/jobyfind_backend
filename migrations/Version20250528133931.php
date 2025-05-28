<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528133931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix user table structure and add missing columns';
    }

    public function up(Schema $schema): void
    {
        // Créer la table user_skill pour la relation ManyToMany seulement si elle n'existe pas
        if (!$schema->hasTable('user_skill')) {
            $this->addSql(<<<'SQL'
                CREATE TABLE user_skill (user_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_BCFF1F2FA76ED395 (user_id), INDEX IDX_BCFF1F2F5585C142 (skill_id), PRIMARY KEY(user_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
            $this->addSql(<<<'SQL'
                ALTER TABLE user_skill ADD CONSTRAINT FK_BCFF1F2FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
            SQL);
            $this->addSql(<<<'SQL'
                ALTER TABLE user_skill ADD CONSTRAINT FK_BCFF1F2F5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE
            SQL);
        }

        // Nettoyer d'abord les données incohérentes dans request_badge
        $this->addSql(<<<'SQL'
            SET FOREIGN_KEY_CHECKS = 0
        SQL);
        
        // Supprimer les contraintes si elles existent
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge DROP FOREIGN KEY IF EXISTS FK_A3E7F438A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge DROP FOREIGN KEY IF EXISTS FK_A3E7F438C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IF EXISTS IDX_A3E7F438C32A47EE ON request_badge
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IF EXISTS IDX_A3E7F438A76ED395 ON request_badge
        SQL);

        // Supprimer les colonnes si elles existent
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge 
            DROP COLUMN IF EXISTS user_id, 
            DROP COLUMN IF EXISTS school_id, 
            DROP COLUMN IF EXISTS status, 
            MODIFY COLUMN request_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
            MODIFY COLUMN response_date DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);

        // Nettoyer les contraintes existantes dans user
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY IF EXISTS FK_8D93D649718F447A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IF EXISTS IDX_8D93D649718F447A ON user
        SQL);

        // Mettre à NULL toutes les valeurs de validated_by_school_id qui ne correspondent pas à un request_badge existant
        $this->addSql(<<<'SQL'
            UPDATE user SET validated_by_school_id = NULL WHERE validated_by_school_id IS NOT NULL AND validated_by_school_id NOT IN (SELECT id FROM request_badge)
        SQL);

        // Ajouter les colonnes manquantes et modifier la structure
        $this->addSql(<<<'SQL'
            ALTER TABLE user 
            ADD COLUMN IF NOT EXISTS location_city VARCHAR(255) DEFAULT NULL, 
            ADD COLUMN IF NOT EXISTS location_region VARCHAR(255) DEFAULT NULL, 
            ADD COLUMN IF NOT EXISTS location_code VARCHAR(255) DEFAULT NULL, 
            ADD COLUMN IF NOT EXISTS contact_email VARCHAR(255) DEFAULT NULL, 
            ADD COLUMN IF NOT EXISTS description LONGTEXT DEFAULT NULL, 
            ADD COLUMN IF NOT EXISTS badge DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', 
            DROP COLUMN IF EXISTS user_type, 
            DROP COLUMN IF EXISTS is_student_badge_validated, 
            CHANGE COLUMN validated_by_school_id request_badge_id INT DEFAULT NULL
        SQL);

        // Ajouter la contrainte de clé étrangère maintenant que les données sont nettoyées
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D64928722D9 FOREIGN KEY (request_badge_id) REFERENCES request_badge (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D64928722D9 ON user (request_badge_id)
        SQL);
        
        $this->addSql(<<<'SQL'
            SET FOREIGN_KEY_CHECKS = 1
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Supprimer la table user_skill
        $this->addSql(<<<'SQL'
            DROP TABLE user_skill
        SQL);

        // Restaurer la structure précédente de request_badge
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge ADD user_id INT DEFAULT NULL, ADD school_id INT DEFAULT NULL, ADD status VARCHAR(255) DEFAULT NULL, CHANGE request_date request_date DATETIME NOT NULL, CHANGE response_date response_date DATETIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge ADD CONSTRAINT FK_A3E7F438A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge ADD CONSTRAINT FK_A3E7F438C32A47EE FOREIGN KEY (school_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A3E7F438A76ED395 ON request_badge (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A3E7F438C32A47EE ON request_badge (school_id)
        SQL);

        // Restaurer la structure précédente de user
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D64928722D9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D64928722D9 ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD user_type VARCHAR(255) DEFAULT NULL, ADD is_student_badge_validated TINYINT(1) DEFAULT NULL, DROP location_city, DROP location_region, DROP location_code, DROP contact_email, DROP description, DROP badge, CHANGE request_badge_id validated_by_school_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D649718F447A FOREIGN KEY (validated_by_school_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D649718F447A ON user (validated_by_school_id)
        SQL);
    }
}
