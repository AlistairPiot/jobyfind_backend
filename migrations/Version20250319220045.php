<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319220045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mission_job_application (mission_id INT NOT NULL, job_application_id INT NOT NULL, INDEX IDX_F1F68AEDBE6CAE90 (mission_id), INDEX IDX_F1F68AEDAC7A5A08 (job_application_id), PRIMARY KEY(mission_id, job_application_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill_skill_category (skill_id INT NOT NULL, skill_category_id INT NOT NULL, INDEX IDX_86DD17995585C142 (skill_id), INDEX IDX_86DD1799AC58042E (skill_category_id), PRIMARY KEY(skill_id, skill_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_skill (user_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_BCFF1F2FA76ED395 (user_id), INDEX IDX_BCFF1F2F5585C142 (skill_id), PRIMARY KEY(user_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mission_job_application ADD CONSTRAINT FK_F1F68AEDBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mission_job_application ADD CONSTRAINT FK_F1F68AEDAC7A5A08 FOREIGN KEY (job_application_id) REFERENCES job_application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_skill_category ADD CONSTRAINT FK_86DD17995585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_skill_category ADD CONSTRAINT FK_86DD1799AC58042E FOREIGN KEY (skill_category_id) REFERENCES skill_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_skill ADD CONSTRAINT FK_BCFF1F2FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_skill ADD CONSTRAINT FK_BCFF1F2F5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_application ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_application ADD CONSTRAINT FK_C737C688A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C737C688A76ED395 ON job_application (user_id)');
        $this->addSql('ALTER TABLE media ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10CA76ED395 ON media (user_id)');
        $this->addSql('ALTER TABLE mission ADD user_id INT DEFAULT NULL, ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_9067F23CA76ED395 ON mission (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9067F23CC54C8C93 ON mission (type_id)');
        $this->addSql('ALTER TABLE user ADD request_badge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64928722D9 FOREIGN KEY (request_badge_id) REFERENCES request_badge (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64928722D9 ON user (request_badge_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mission_job_application DROP FOREIGN KEY FK_F1F68AEDBE6CAE90');
        $this->addSql('ALTER TABLE mission_job_application DROP FOREIGN KEY FK_F1F68AEDAC7A5A08');
        $this->addSql('ALTER TABLE skill_skill_category DROP FOREIGN KEY FK_86DD17995585C142');
        $this->addSql('ALTER TABLE skill_skill_category DROP FOREIGN KEY FK_86DD1799AC58042E');
        $this->addSql('ALTER TABLE user_skill DROP FOREIGN KEY FK_BCFF1F2FA76ED395');
        $this->addSql('ALTER TABLE user_skill DROP FOREIGN KEY FK_BCFF1F2F5585C142');
        $this->addSql('DROP TABLE mission_job_application');
        $this->addSql('DROP TABLE skill_skill_category');
        $this->addSql('DROP TABLE user_skill');
        $this->addSql('ALTER TABLE job_application DROP FOREIGN KEY FK_C737C688A76ED395');
        $this->addSql('DROP INDEX IDX_C737C688A76ED395 ON job_application');
        $this->addSql('ALTER TABLE job_application DROP user_id');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA76ED395');
        $this->addSql('DROP INDEX IDX_6A2CA10CA76ED395 ON media');
        $this->addSql('ALTER TABLE media DROP user_id');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CA76ED395');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CC54C8C93');
        $this->addSql('DROP INDEX IDX_9067F23CA76ED395 ON mission');
        $this->addSql('DROP INDEX UNIQ_9067F23CC54C8C93 ON mission');
        $this->addSql('ALTER TABLE mission DROP user_id, DROP type_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64928722D9');
        $this->addSql('DROP INDEX UNIQ_8D93D64928722D9 ON user');
        $this->addSql('ALTER TABLE user DROP request_badge_id');
    }
}
