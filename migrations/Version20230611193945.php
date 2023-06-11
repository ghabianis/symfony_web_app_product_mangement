<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230611193945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE coupons_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE coupons_types_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE images_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE products_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categories (id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, category_order INT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)');
        $this->addSql('CREATE TABLE coupons (id INT NOT NULL, coupons_types_id INT NOT NULL, code VARCHAR(10) NOT NULL, description TEXT NOT NULL, discount INT NOT NULL, max_usage INT NOT NULL, validity TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_valid BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F564111877153098 ON coupons (code)');
        $this->addSql('CREATE INDEX IDX_F56411183DDD47B7 ON coupons (coupons_types_id)');
        $this->addSql('COMMENT ON COLUMN coupons.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE coupons_types (id INT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE images (id INT NOT NULL, products_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E01FBE6A6C8A81A9 ON images (products_id)');
        $this->addSql('CREATE TABLE orders (id INT NOT NULL, coupons_id INT DEFAULT NULL, users_id INT NOT NULL, reference VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEEAEA34913 ON orders (reference)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6D72B15C ON orders (coupons_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE67B3B43D ON orders (users_id)');
        $this->addSql('COMMENT ON COLUMN orders.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE orders_details (orders_id INT NOT NULL, products_id INT NOT NULL, quantity INT NOT NULL, price INT NOT NULL, PRIMARY KEY(orders_id, products_id))');
        $this->addSql('CREATE INDEX IDX_835379F1CFFE9AD6 ON orders_details (orders_id)');
        $this->addSql('CREATE INDEX IDX_835379F16C8A81A9 ON orders_details (products_id)');
        $this->addSql('CREATE TABLE products (id INT NOT NULL, categories_id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price INT NOT NULL, stock INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B3BA5A5AA21214B7 ON products (categories_id)');
        $this->addSql('COMMENT ON COLUMN products.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(100) NOT NULL, firstname VARCHAR(100) NOT NULL, address VARCHAR(255) NOT NULL, zipcode VARCHAR(5) NOT NULL, city VARCHAR(150) NOT NULL, is_verified BOOLEAN NOT NULL, reset_token VARCHAR(100) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE coupons ADD CONSTRAINT FK_F56411183DDD47B7 FOREIGN KEY (coupons_types_id) REFERENCES coupons_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6D72B15C FOREIGN KEY (coupons_id) REFERENCES coupons (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders_details ADD CONSTRAINT FK_835379F1CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orders_details ADD CONSTRAINT FK_835379F16C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE categories DROP CONSTRAINT FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5AA21214B7');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE6D72B15C');
        $this->addSql('ALTER TABLE coupons DROP CONSTRAINT FK_F56411183DDD47B7');
        $this->addSql('ALTER TABLE orders_details DROP CONSTRAINT FK_835379F1CFFE9AD6');
        $this->addSql('ALTER TABLE images DROP CONSTRAINT FK_E01FBE6A6C8A81A9');
        $this->addSql('ALTER TABLE orders_details DROP CONSTRAINT FK_835379F16C8A81A9');
        $this->addSql('ALTER TABLE orders DROP CONSTRAINT FK_E52FFDEE67B3B43D');
        $this->addSql('DROP SEQUENCE categories_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE coupons_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE coupons_types_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE images_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE orders_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE products_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE coupons');
        $this->addSql('DROP TABLE coupons_types');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE orders_details');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
