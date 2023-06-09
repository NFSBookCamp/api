<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609073139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bookcamp_accounts (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6308E941A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bookcamp_disciplines (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, time INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bookcamp_history (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, discipline_id INT NOT NULL, booked_by_id INT NOT NULL, room_booked_at DATETIME DEFAULT NULL, room_booking_delay INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5B63CEA754177093 (room_id), UNIQUE INDEX UNIQ_5B63CEA7A5522701 (discipline_id), UNIQUE INDEX UNIQ_5B63CEA7F4A5BD90 (booked_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bookcamp_rooms (id INT AUTO_INCREMENT NOT NULL, booked_by_id INT DEFAULT NULL, discipline_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, booked_at DATETIME DEFAULT NULL, booking_delay INT DEFAULT NULL, caracteristics LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_2DADC751F4A5BD90 (booked_by_id), INDEX IDX_2DADC751A5522701 (discipline_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bookcamp_rooms_participants (room_id INT NOT NULL, account_id INT NOT NULL, INDEX IDX_5B58B27F54177093 (room_id), INDEX IDX_5B58B27F9B6B5FBA (account_id), PRIMARY KEY(room_id, account_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bookcamp_users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_458F782EE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bookcamp_accounts ADD CONSTRAINT FK_6308E941A76ED395 FOREIGN KEY (user_id) REFERENCES bookcamp_users (id)');
        $this->addSql('ALTER TABLE bookcamp_history ADD CONSTRAINT FK_5B63CEA754177093 FOREIGN KEY (room_id) REFERENCES bookcamp_rooms (id)');
        $this->addSql('ALTER TABLE bookcamp_history ADD CONSTRAINT FK_5B63CEA7A5522701 FOREIGN KEY (discipline_id) REFERENCES bookcamp_disciplines (id)');
        $this->addSql('ALTER TABLE bookcamp_history ADD CONSTRAINT FK_5B63CEA7F4A5BD90 FOREIGN KEY (booked_by_id) REFERENCES bookcamp_accounts (id)');
        $this->addSql('ALTER TABLE bookcamp_rooms ADD CONSTRAINT FK_2DADC751F4A5BD90 FOREIGN KEY (booked_by_id) REFERENCES bookcamp_accounts (id)');
        $this->addSql('ALTER TABLE bookcamp_rooms ADD CONSTRAINT FK_2DADC751A5522701 FOREIGN KEY (discipline_id) REFERENCES bookcamp_disciplines (id)');
        $this->addSql('ALTER TABLE bookcamp_rooms_participants ADD CONSTRAINT FK_5B58B27F54177093 FOREIGN KEY (room_id) REFERENCES bookcamp_rooms (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bookcamp_rooms_participants ADD CONSTRAINT FK_5B58B27F9B6B5FBA FOREIGN KEY (account_id) REFERENCES bookcamp_accounts (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookcamp_accounts DROP FOREIGN KEY FK_6308E941A76ED395');
        $this->addSql('ALTER TABLE bookcamp_history DROP FOREIGN KEY FK_5B63CEA754177093');
        $this->addSql('ALTER TABLE bookcamp_history DROP FOREIGN KEY FK_5B63CEA7A5522701');
        $this->addSql('ALTER TABLE bookcamp_history DROP FOREIGN KEY FK_5B63CEA7F4A5BD90');
        $this->addSql('ALTER TABLE bookcamp_rooms DROP FOREIGN KEY FK_2DADC751F4A5BD90');
        $this->addSql('ALTER TABLE bookcamp_rooms DROP FOREIGN KEY FK_2DADC751A5522701');
        $this->addSql('ALTER TABLE bookcamp_rooms_participants DROP FOREIGN KEY FK_5B58B27F54177093');
        $this->addSql('ALTER TABLE bookcamp_rooms_participants DROP FOREIGN KEY FK_5B58B27F9B6B5FBA');
        $this->addSql('DROP TABLE bookcamp_accounts');
        $this->addSql('DROP TABLE bookcamp_disciplines');
        $this->addSql('DROP TABLE bookcamp_history');
        $this->addSql('DROP TABLE bookcamp_rooms');
        $this->addSql('DROP TABLE bookcamp_rooms_participants');
        $this->addSql('DROP TABLE bookcamp_users');
    }
}
