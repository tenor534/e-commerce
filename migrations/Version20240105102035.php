<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240105102035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoices (id INT AUTO_INCREMENT NOT NULL, orders_id INT NOT NULL, reference VARCHAR(150) NOT NULL, shipping INT NOT NULL, tracking_number VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', shipped_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6A2F2F95CFFE9AD6 (orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refunds (id INT AUTO_INCREMENT NOT NULL, orders_id INT NOT NULL, refunds_type INT NOT NULL, reference VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7EE53AD9CFFE9AD6 (orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refunds_details (refunds_id INT NOT NULL, products_id INT NOT NULL, quantity INT NOT NULL, price INT NOT NULL, INDEX IDX_1344A94311D0037F (refunds_id), INDEX IDX_1344A9436C8A81A9 (products_id), PRIMARY KEY(refunds_id, products_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoices ADD CONSTRAINT FK_6A2F2F95CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE refunds ADD CONSTRAINT FK_7EE53AD9CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE refunds_details ADD CONSTRAINT FK_1344A94311D0037F FOREIGN KEY (refunds_id) REFERENCES refunds (id)');
        $this->addSql('ALTER TABLE refunds_details ADD CONSTRAINT FK_1344A9436C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoices DROP FOREIGN KEY FK_6A2F2F95CFFE9AD6');
        $this->addSql('ALTER TABLE refunds DROP FOREIGN KEY FK_7EE53AD9CFFE9AD6');
        $this->addSql('ALTER TABLE refunds_details DROP FOREIGN KEY FK_1344A94311D0037F');
        $this->addSql('ALTER TABLE refunds_details DROP FOREIGN KEY FK_1344A9436C8A81A9');
        $this->addSql('DROP TABLE invoices');
        $this->addSql('DROP TABLE refunds');
        $this->addSql('DROP TABLE refunds_details');
    }
}
