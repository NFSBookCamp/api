<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609193819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookcamp_history DROP INDEX UNIQ_5B63CEA7A5522701, ADD INDEX IDX_5B63CEA7A5522701 (discipline_id)');
        $this->addSql('ALTER TABLE bookcamp_history DROP INDEX UNIQ_5B63CEA7F4A5BD90, ADD INDEX IDX_5B63CEA7F4A5BD90 (booked_by_id)');
        $this->addSql('ALTER TABLE bookcamp_history DROP INDEX UNIQ_5B63CEA754177093, ADD INDEX IDX_5B63CEA754177093 (room_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookcamp_history DROP INDEX IDX_5B63CEA754177093, ADD UNIQUE INDEX UNIQ_5B63CEA754177093 (room_id)');
        $this->addSql('ALTER TABLE bookcamp_history DROP INDEX IDX_5B63CEA7A5522701, ADD UNIQUE INDEX UNIQ_5B63CEA7A5522701 (discipline_id)');
        $this->addSql('ALTER TABLE bookcamp_history DROP INDEX IDX_5B63CEA7F4A5BD90, ADD UNIQUE INDEX UNIQ_5B63CEA7F4A5BD90 (booked_by_id)');
    }
}
