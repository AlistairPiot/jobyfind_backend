<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306220458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role JSON NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', location_city VARCHAR(255) DEFAULT NULL, location_region VARCHAR(255) DEFAULT NULL, location_code VARCHAR(255) DEFAULT NULL, name_company VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, badge TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }
}
