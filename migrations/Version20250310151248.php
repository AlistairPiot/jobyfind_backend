<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250310151248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD name VARCHAR(255) NOT NULL, DROP type');
        $this->addSql('ALTER TABLE user DROP role, CHANGE badge badge DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD type JSON NOT NULL COMMENT \'(DC2Type:json)\', DROP name');
        $this->addSql('ALTER TABLE user ADD role JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE badge badge TINYINT(1) NOT NULL');
    }
}
