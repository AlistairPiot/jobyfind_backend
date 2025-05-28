<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250528122819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Les contraintes FK_A3E7F438A76ED395 et FK_A3E7F438C32A47EE existent déjà, on les ignore
        // La contrainte FK_8D93D64928722D9 n'existe pas, on l'ignore aussi
        // Les colonnes user_type et is_student_badge_validated existent déjà, on les ignore
        
        // Supprimer les anciennes colonnes et modifier les autres (seulement celles qui existent)
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP created_at, DROP location_city, DROP location_region, DROP location_code, DROP name_company, DROP contact_email, DROP description, DROP badge, DROP name_school, CHANGE email email VARCHAR(180) NOT NULL, CHANGE first_name first_name VARCHAR(255) NOT NULL, CHANGE last_name last_name VARCHAR(255) NOT NULL, CHANGE request_badge_id validated_by_school_id INT DEFAULT NULL
        SQL);
        
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D649718F447A FOREIGN KEY (validated_by_school_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D649718F447A ON user (validated_by_school_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D649718F447A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8D93D649718F447A ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD location_city VARCHAR(255) DEFAULT NULL, ADD location_region VARCHAR(255) DEFAULT NULL, ADD location_code VARCHAR(255) DEFAULT NULL, ADD name_company VARCHAR(255) DEFAULT NULL, ADD contact_email VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD badge DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', ADD name_school VARCHAR(255) DEFAULT NULL, DROP user_type, DROP is_student_badge_validated, CHANGE email email VARCHAR(255) NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE validated_by_school_id request_badge_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D64928722D9 FOREIGN KEY (request_badge_id) REFERENCES request_badge (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D64928722D9 ON user (request_badge_id)
        SQL);
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
    }
}
