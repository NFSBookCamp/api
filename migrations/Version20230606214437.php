<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606214437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_account (room_id INT NOT NULL, account_id INT NOT NULL, INDEX IDX_9114EA4454177093 (room_id), INDEX IDX_9114EA449B6B5FBA (account_id), PRIMARY KEY(room_id, account_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_account ADD CONSTRAINT FK_9114EA4454177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_account ADD CONSTRAINT FK_9114EA449B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_account DROP FOREIGN KEY FK_9114EA4454177093');
        $this->addSql('ALTER TABLE room_account DROP FOREIGN KEY FK_9114EA449B6B5FBA');
        $this->addSql('DROP TABLE room_account');
    }
}
