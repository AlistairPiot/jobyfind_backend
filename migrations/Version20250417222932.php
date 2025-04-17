<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417222932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE type_mission');
        $this->addSql('ALTER TABLE media ADD name VARCHAR(255) NOT NULL, DROP type');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9067F23CC54C8C93 ON mission (type_id)');
        $this->addSql('ALTER TABLE mission RENAME INDEX fk_9067f23ca76ed395 TO IDX_9067F23CA76ED395');
        $this->addSql('ALTER TABLE user ADD request_badge_id INT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE badge badge DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE role roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64928722D9 FOREIGN KEY (request_badge_id) REFERENCES request_badge (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64928722D9 ON user (request_badge_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CC54C8C93');
        $this->addSql('CREATE TABLE type_mission (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE type');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64928722D9');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D64928722D9 ON user');
        $this->addSql('ALTER TABLE user DROP request_badge_id, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE badge badge TINYINT(1) NOT NULL, CHANGE roles role JSON NOT NULL');
        $this->addSql('DROP INDEX UNIQ_9067F23CC54C8C93 ON mission');
        $this->addSql('ALTER TABLE mission RENAME INDEX idx_9067f23ca76ed395 TO FK_9067F23CA76ED395');
        $this->addSql('ALTER TABLE media ADD type JSON NOT NULL, DROP name');
    }
}
