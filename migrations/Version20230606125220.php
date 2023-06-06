<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230606125220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7D3656A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, time INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, discipline_id INT NOT NULL, booked_by_id INT NOT NULL, room_booked_at DATETIME DEFAULT NULL, room_booking_delay INT DEFAULT NULL, UNIQUE INDEX UNIQ_27BA704B54177093 (room_id), UNIQUE INDEX UNIQ_27BA704BA5522701 (discipline_id), UNIQUE INDEX UNIQ_27BA704BF4A5BD90 (booked_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, booked_by_id INT DEFAULT NULL, discipline_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, booked_at DATETIME DEFAULT NULL, booking_delay INT DEFAULT NULL, caracteristics LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_729F519BF4A5BD90 (booked_by_id), INDEX IDX_729F519BA5522701 (discipline_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BF4A5BD90 FOREIGN KEY (booked_by_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BF4A5BD90 FOREIGN KEY (booked_by_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4A76ED395');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704B54177093');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BA5522701');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BF4A5BD90');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BF4A5BD90');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BA5522701');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE user');
    }
}
