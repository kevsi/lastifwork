<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419184640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members DROP FOREIGN KEY FK_9D9D39F482D40A1F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members DROP FOREIGN KEY FK_9D9D39F4A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members ADD role VARCHAR(50) DEFAULT 'member' NOT NULL, ADD joined_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members ADD CONSTRAINT FK_9D9D39F482D40A1F FOREIGN KEY (workspace_id) REFERENCES workspaces (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members ADD CONSTRAINT FK_9D9D39F4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspaces DROP role, DROP joined_at
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE workspaces ADD role VARCHAR(50) DEFAULT NULL, ADD joined_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members DROP FOREIGN KEY FK_9D9D39F482D40A1F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members DROP FOREIGN KEY FK_9D9D39F4A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members DROP role, DROP joined_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members ADD CONSTRAINT FK_9D9D39F482D40A1F FOREIGN KEY (workspace_id) REFERENCES workspaces (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE workspace_members ADD CONSTRAINT FK_9D9D39F4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
    }
}
