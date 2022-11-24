<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221122173813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'User and role tables creation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role (id SERIAL NOT NULL, uuid UUID NOT NULL, code VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'NOW()\' NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6AD17F50A6 ON role (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A77153098 ON role (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6AEA750E8 ON role (label)');
        $this->addSql('COMMENT ON COLUMN role.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN role.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, role_id INT NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'NOW()\' NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9D17F50A6 ON users (uuid)');
        $this->addSql('CREATE INDEX IDX_1483A5E9D60322AC ON users (role_id)');
        $this->addSql('COMMENT ON COLUMN users.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql("
            INSERT INTO role (uuid, code, label) VALUES
            (uuid('2e5de629-d0d6-4ae7-abeb-ab7fb0139676'), 'ROLE_GUEST', 'guest'),
            (uuid('f8d55ab0-abd5-42ca-b35d-63564d45730f'), 'ROLE_USER', 'user'),
            (uuid('28d51ef8-8646-457f-871d-bdf708bd24fe'), 'ROLE_ADMIN', 'admin');
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9D60322AC');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE users');
    }
}
