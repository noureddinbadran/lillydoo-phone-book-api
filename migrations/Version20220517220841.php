<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220517220841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `client` (`id` int(11) NOT NULL AUTO_INCREMENT,`username` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,`password` varchar(96) COLLATE utf8mb4_unicode_ci NOT NULL,`created` datetime NOT NULL,`first_name` varchar(96) COLLATE utf8mb4_unicode_ci NOT NULL,`last_name` varchar(96) COLLATE utf8mb4_unicode_ci NOT NULL,PRIMARY KEY (`id`),UNIQUE KEY `UNIQ_70E4FA78F85E0677` (`username`),KEY `username_idx` (`username`))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE client');
    }
}
