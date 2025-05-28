<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528134241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Rendre les champs first_name et last_name nullable
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL
        SQL);
        
        // Nettoyer les données incohérentes avant d'ajouter la contrainte
        $this->addSql(<<<'SQL'
            UPDATE user SET request_badge_id = NULL WHERE request_badge_id IS NOT NULL AND request_badge_id NOT IN (SELECT id FROM request_badge)
        SQL);
        
        // Ajouter la contrainte de clé étrangère seulement si elle n'existe pas déjà
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D64928722D9 FOREIGN KEY (request_badge_id) REFERENCES request_badge (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D64928722D9 ON user (request_badge_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D64928722D9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D64928722D9 ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE first_name first_name VARCHAR(255) NOT NULL, CHANGE last_name last_name VARCHAR(255) NOT NULL
        SQL);
    }
}
