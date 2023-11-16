<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230118105803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facility (id INT AUTO_INCREMENT NOT NULL, objective_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, sector VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_105994B273484933 (objective_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objective (id INT AUTO_INCREMENT NOT NULL, temperature INT NOT NULL, humidity INT NOT NULL, e_co2 INT NOT NULL, gap_temperature INT NOT NULL, gap_humidity INT NOT NULL, gap_eco2 INT NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, personal TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, facility_id INT NOT NULL, objective_id INT DEFAULT NULL, surface INT DEFAULT NULL, nb_windows INT DEFAULT NULL, name VARCHAR(25) NOT NULL, floor INT NOT NULL, private TINYINT(1) NOT NULL, INDEX IDX_729F519BA7014910 (facility_id), UNIQUE INDEX UNIQ_729F519B73484933 (objective_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_user (room_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EE973C2D54177093 (room_id), INDEX IDX_EE973C2DA76ED395 (user_id), PRIMARY KEY(room_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sensor (id INT AUTO_INCREMENT NOT NULL, room_id INT NOT NULL, num VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, tag INT NOT NULL, UNIQUE INDEX UNIQ_BC8617B054177093 (room_id), UNIQUE INDEX sensor_tag_unique (tag), UNIQUE INDEX sensor_num_unique (num), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facility ADD CONSTRAINT FK_105994B273484933 FOREIGN KEY (objective_id) REFERENCES objective (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BA7014910 FOREIGN KEY (facility_id) REFERENCES facility (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B73484933 FOREIGN KEY (objective_id) REFERENCES objective (id)');
        $this->addSql('ALTER TABLE room_user ADD CONSTRAINT FK_EE973C2D54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_user ADD CONSTRAINT FK_EE973C2DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sensor ADD CONSTRAINT FK_BC8617B054177093 FOREIGN KEY (room_id) REFERENCES room (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facility DROP FOREIGN KEY FK_105994B273484933');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BA7014910');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B73484933');
        $this->addSql('ALTER TABLE room_user DROP FOREIGN KEY FK_EE973C2D54177093');
        $this->addSql('ALTER TABLE room_user DROP FOREIGN KEY FK_EE973C2DA76ED395');
        $this->addSql('ALTER TABLE sensor DROP FOREIGN KEY FK_BC8617B054177093');
        $this->addSql('DROP TABLE facility');
        $this->addSql('DROP TABLE objective');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_user');
        $this->addSql('DROP TABLE sensor');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
