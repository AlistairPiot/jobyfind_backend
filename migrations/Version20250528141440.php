<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528141440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Nettoyer d'abord les références dans la table user
        $this->addSql(<<<'SQL'
            UPDATE user SET request_badge_id = NULL WHERE request_badge_id IS NOT NULL
        SQL);
        
        // Supprimer les demandes de badge existantes qui n'ont pas les relations nécessaires
        $this->addSql(<<<'SQL'
            DELETE FROM request_badge WHERE user_id IS NULL OR school_id IS NULL
        SQL);
        
        // Rendre les colonnes NOT NULL
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge CHANGE user_id user_id INT NOT NULL, CHANGE school_id school_id INT NOT NULL
        SQL);
        
        // Ajouter les contraintes de clé étrangère
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge DROP FOREIGN KEY FK_A3E7F438A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge DROP FOREIGN KEY FK_A3E7F438C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A3E7F438A76ED395 ON request_badge
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A3E7F438C32A47EE ON request_badge
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE request_badge CHANGE user_id user_id INT DEFAULT NULL, CHANGE school_id school_id INT DEFAULT NULL
        SQL);
    }
}
