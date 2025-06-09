<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609113940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE mission_skill (mission_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_CEABBB4ABE6CAE90 (mission_id), INDEX IDX_CEABBB4A5585C142 (skill_id), PRIMARY KEY(mission_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission_skill ADD CONSTRAINT FK_CEABBB4ABE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission_skill ADD CONSTRAINT FK_CEABBB4A5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission_recommendation CHANGE recommended_at recommended_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE mission_skill DROP FOREIGN KEY FK_CEABBB4ABE6CAE90
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission_skill DROP FOREIGN KEY FK_CEABBB4A5585C142
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mission_skill
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mission_recommendation CHANGE recommended_at recommended_at DATETIME NOT NULL
        SQL);
    }
}
