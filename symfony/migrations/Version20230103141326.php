<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230103141326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sensor DROP INDEX IDX_BC8617B054177093, ADD UNIQUE INDEX UNIQ_BC8617B054177093 (room_id)');
        $this->addSql('CREATE UNIQUE INDEX sensor_num_unique ON sensor (num)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sensor DROP INDEX UNIQ_BC8617B054177093, ADD INDEX IDX_BC8617B054177093 (room_id)');
        $this->addSql('DROP INDEX sensor_num_unique ON sensor');
    }
}
