<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121102300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insertion des données de test pour les types, catégories de compétences et compétences';
    }

    public function up(Schema $schema): void
    {
        // Insertion des types de contrat
        $this->addSql(<<<'SQL'
            INSERT INTO `type` (`id`, `name`) VALUES
            (1, 'CDI'),
            (2, 'CDD'),
            (3, 'Alternance'),
            (4, 'Stage'),
            (5, 'Freelance')
        SQL);

        // Insertion des catégories de compétences
        $this->addSql(<<<'SQL'
            INSERT INTO `skill_category` (`id`, `name`, `description`) VALUES
            (1, 'Backend', 'Développement côté serveur et API.'),
            (2, 'Frontend', 'Développement d''interfaces utilisateur et interactions.'),
            (3, 'Base de données', 'Gestion et optimisation des bases de données.'),
            (4, 'DevOps & Cloud', 'Automatisation, CI/CD et infrastructure cloud.'),
            (5, 'UI/UX Design', 'Conception d''interfaces utilisateur et expérience utilisateur.'),
            (6, 'Mobile Development', 'Développement d''applications mobiles iOS et Android.'),
            (7, 'Cybersécurité', 'Sécurisation des applications et des infrastructures.'),
            (8, 'Data Science & IA', 'Analyse de données et intelligence artificielle.'),
            (9, 'Marketing Digital', 'SEO, publicité en ligne et stratégies digitales.'),
            (10, 'Gestion de projet & No-Code', 'Outils et méthodologies de gestion de projet.')
        SQL);

        // Insertion des compétences
        $this->addSql(<<<'SQL'
            INSERT INTO `skill` (`id`, `name`) VALUES
            (1, 'SkillsTest'),
            (2, 'SkillTest2'),
            (3, 'SkillTest3'),
            (4, 'test4'),
            (5, 'PHP'),
            (6, 'Symfony'),
            (7, 'Laravel'),
            (8, 'Node.js'),
            (9, 'Express.js'),
            (10, 'Java'),
            (11, 'Spring Boot'),
            (12, 'Python (Django, Flask)'),
            (13, 'Ruby on Rails'),
            (14, '.NET (C#)'),
            (15, 'Go'),
            (16, 'GraphQL'),
            (17, 'REST API'),
            (18, 'HTML5'),
            (19, 'CSS3'),
            (20, 'JavaScript'),
            (21, 'TypeScript'),
            (22, 'React.js'),
            (23, 'Next.js'),
            (24, 'Vue.js'),
            (25, 'Angular'),
            (26, 'Svelte'),
            (27, 'Tailwind CSS'),
            (28, 'Bootstrap'),
            (29, 'Material UI'),
            (30, 'Three.js'),
            (31, 'MySQL'),
            (32, 'PostgreSQL'),
            (33, 'MongoDB'),
            (34, 'Firebase'),
            (35, 'Redis'),
            (36, 'Elasticsearch'),
            (37, 'SQLite'),
            (38, 'Docker'),
            (39, 'Kubernetes'),
            (40, 'AWS'),
            (41, 'Google Cloud Platform (GCP)'),
            (42, 'Microsoft Azure'),
            (43, 'CI/CD (GitHub Actions, GitLab CI, Jenkins)'),
            (44, 'Terraform'),
            (45, 'Ansible'),
            (46, 'Figma'),
            (47, 'Adobe XD'),
            (48, 'Sketch'),
            (49, 'Webflow'),
            (50, 'Framer'),
            (51, 'Design System'),
            (52, 'React Native'),
            (53, 'Flutter'),
            (54, 'Swift (iOS)'),
            (55, 'Kotlin (Android)'),
            (56, 'Sécurité des applications web'),
            (57, 'Pentesting'),
            (58, 'OWASP'),
            (59, 'Cryptographie'),
            (60, 'Python (Pandas, NumPy, Scikit-learn)'),
            (61, 'TensorFlow'),
            (62, 'PyTorch'),
            (63, 'Big Data'),
            (64, 'Machine Learning'),
            (65, 'SEO'),
            (66, 'Google Ads'),
            (67, 'Facebook Ads'),
            (68, 'Copywriting'),
            (69, 'Email Marketing'),
            (70, 'Notion'),
            (71, 'Trello'),
            (72, 'Airtable'),
            (73, 'Zapier'),
            (74, 'Make (ex-Integromat)')
        SQL);

        // Mise à jour des auto-increment
        $this->addSql(<<<'SQL'
            ALTER TABLE `type` AUTO_INCREMENT = 6
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE `skill_category` AUTO_INCREMENT = 11
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE `skill` AUTO_INCREMENT = 75
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Suppression des données insérées
        $this->addSql(<<<'SQL'
            DELETE FROM `skill` WHERE id BETWEEN 1 AND 74
        SQL);

        $this->addSql(<<<'SQL'
            DELETE FROM `skill_category` WHERE id BETWEEN 1 AND 10
        SQL);

        $this->addSql(<<<'SQL'
            DELETE FROM `type` WHERE id BETWEEN 1 AND 5
        SQL);

        // Reset des auto-increment
        $this->addSql(<<<'SQL'
            ALTER TABLE `skill` AUTO_INCREMENT = 1
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE `skill_category` AUTO_INCREMENT = 1
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE `type` AUTO_INCREMENT = 1
        SQL);
    }
} 