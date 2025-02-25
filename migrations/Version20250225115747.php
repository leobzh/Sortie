<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225115747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55CAF762A4D60759 ON etat (libelle)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_694309E46C6E55B5 ON site (nom)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C3FD3F26C6E55B5 ON sortie (nom)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3F6BD1646 ON utilisateur (site_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_43C3D9C36C6E55B5 ON ville (nom)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3F6BD1646');
        $this->addSql('DROP INDEX IDX_1D1C63B3F6BD1646 ON utilisateur');
        $this->addSql('DROP INDEX UNIQ_55CAF762A4D60759 ON etat');
        $this->addSql('DROP INDEX UNIQ_3C3FD3F26C6E55B5 ON sortie');
        $this->addSql('DROP INDEX UNIQ_43C3D9C36C6E55B5 ON ville');
        $this->addSql('DROP INDEX UNIQ_694309E46C6E55B5 ON site');
    }
}
