<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191212143949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649964804A5');
        $this->addSql('DROP INDEX IDX_8D93D649964804A5 ON user');
        $this->addSql('ALTER TABLE user ADD avatar_perso VARCHAR(255) DEFAULT NULL, DROP user_avatar_img_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD user_avatar_img_id INT DEFAULT NULL, DROP avatar_perso');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649964804A5 FOREIGN KEY (user_avatar_img_id) REFERENCES user_avatar (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649964804A5 ON user (user_avatar_img_id)');
    }
}
