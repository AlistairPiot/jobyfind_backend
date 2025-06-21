<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121104600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Association des compétences aux catégories appropriées';
    }

    public function up(Schema $schema): void
    {
        // Association des compétences Backend (catégorie 1)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (5, 1), (6, 1), (7, 1), (8, 1), (9, 1), (10, 1), (11, 1), (12, 1), (13, 1), (14, 1), (15, 1), (16, 1), (17, 1)
        SQL);

        // Association des compétences Frontend (catégorie 2)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (18, 2), (19, 2), (20, 2), (21, 2), (22, 2), (23, 2), (24, 2), (25, 2), (26, 2), (27, 2), (28, 2), (29, 2), (30, 2)
        SQL);

        // Association des compétences Base de données (catégorie 3)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (31, 3), (32, 3), (33, 3), (34, 3), (35, 3), (36, 3), (37, 3)
        SQL);

        // Association des compétences DevOps & Cloud (catégorie 4)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (38, 4), (39, 4), (40, 4), (41, 4), (42, 4), (43, 4), (44, 4), (45, 4)
        SQL);

        // Association des compétences UI/UX Design (catégorie 5)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (46, 5), (47, 5), (48, 5), (49, 5), (50, 5), (51, 5)
        SQL);

        // Association des compétences Mobile Development (catégorie 6)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (52, 6), (53, 6), (54, 6), (55, 6)
        SQL);

        // Association des compétences Cybersécurité (catégorie 7)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (56, 7), (57, 7), (58, 7), (59, 7)
        SQL);

        // Association des compétences Data Science & IA (catégorie 8)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (60, 8), (61, 8), (62, 8), (63, 8), (64, 8)
        SQL);

        // Association des compétences Marketing Digital (catégorie 9)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (65, 9), (66, 9), (67, 9), (68, 9), (69, 9)
        SQL);

        // Association des compétences Gestion de projet & No-Code (catégorie 10)
        $this->addSql(<<<'SQL'
            INSERT INTO skill_skill_category (skill_id, skill_category_id) VALUES
            (70, 10), (71, 10), (72, 10), (73, 10), (74, 10)
        SQL);

        // Les compétences de test (1-4) ne sont associées à aucune catégorie
    }

    public function down(Schema $schema): void
    {
        // Suppression de toutes les associations
        $this->addSql(<<<'SQL'
            DELETE FROM skill_skill_category WHERE skill_id BETWEEN 1 AND 74
        SQL);
    }
} 