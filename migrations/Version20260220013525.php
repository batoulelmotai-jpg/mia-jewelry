<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260220013525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, order_ref_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_62809DB0E238517C (order_ref_id), INDEX IDX_62809DB04584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `orders` (id INT AUTO_INCREMENT NOT NULL, customer_name VARCHAR(250) NOT NULL, phone VARCHAR(40) NOT NULL, city VARCHAR(120) DEFAULT NULL, status VARCHAR(25) NOT NULL, total NUMERIC(10, 2) NOT NULL, address LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0E238517C FOREIGN KEY (order_ref_id) REFERENCES `orders` (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, customer_name VARCHAR(250) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, phone VARCHAR(40) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, city VARCHAR(120) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, status VARCHAR(25) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, total NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_ref_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_52EA1F094584665A (product_id), INDEX IDX_52EA1F09E238517C (order_ref_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0E238517C');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB04584665A');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE `orders`');
    }
}
