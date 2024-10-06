<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('oauth2/client')}` (
        `entity_id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(200),
        `secret` VARCHAR(80) NOT NULL,
        `redirect_uri` VARCHAR(2000),
        `grant_types` VARCHAR(2000),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_oauth2_client_created_at` (`created_at`)
    ) ENGINE=InnoDB;
");

$installer->run("
    CREATE TABLE `{$installer->getTable('oauth2/auth_code')}` (
        `authorization_code` VARCHAR(100) NOT NULL,
        `customer_id` INT(10) UNSIGNED,
        `redirect_uri` VARCHAR(2000),
        `client_id` BIGINT NOT NULL,
        `expires_in` INT NOT NULL,
        `used` BOOLEAN DEFAULT FALSE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`authorization_code`),
        CONSTRAINT `fk_oauth2_auth_code_client_id` FOREIGN KEY (`client_id`) REFERENCES `{$installer->getTable('oauth2/client')}` (`entity_id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
        CONSTRAINT `fk_oauth2_auth_code_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `{$installer->getTable('customer/entity')}` (`entity_id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
        INDEX `idx_oauth2_auth_code_created_at` (`created_at`),
        INDEX `idx_oauth2_auth_code_client_id` (`client_id`),
        INDEX `idx_oauth2_auth_code_admin_id` (`admin_id`),
        INDEX `idx_oauth2_auth_code_customer_id` (`customer_id`)
    ) ENGINE=InnoDB;
");

$installer->run("
    CREATE TABLE `{$installer->getTable('oauth2/access_token')}` (
        `access_token` VARCHAR(100) NOT NULL,
        `refresh_token` VARCHAR(100),
        `admin_id` INT(10) UNSIGNED,
        `customer_id` INT(10) UNSIGNED,
        `client_id` BIGINT NOT NULL,
        `expires_in` INT NOT NULL,
        `revoked` BOOLEAN DEFAULT FALSE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`access_token`),
        CONSTRAINT `fk_oauth2_access_token_client_id` FOREIGN KEY (`client_id`) REFERENCES `{$installer->getTable('oauth2/client')}` (`entity_id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
        CONSTRAINT `fk_oauth2_access_token_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `{$installer->getTable('admin/user')}` (`user_id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
        CONSTRAINT `fk_oauth2_access_token_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `{$installer->getTable('customer/entity')}` (`entity_id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
        INDEX `idx_oauth2_access_token_created_at` (`created_at`),
        INDEX `idx_oauth2_access_token_client_id` (`client_id`),
        INDEX `idx_oauth2_access_token_admin_id` (`admin_id`),
        INDEX `idx_oauth2_access_token_customer_id` (`customer_id`)
    ) ENGINE=InnoDB;
");


$installer->run("
CREATE TABLE `{$installer->getTable('oauth2/device_code')}` (
    `device_code` VARCHAR(32) PRIMARY KEY NOT NULL,
    `admin_id` INT(10) UNSIGNED,
    `customer_id` INT(10) UNSIGNED,
    `user_code` VARCHAR(8) NOT NULL,
    `client_id` BIGINT NOT NULL,
    `expires_in` INT NOT NULL,
    `authorized` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_oauth2_device_code_user_code` (`user_code`),
    CONSTRAINT `fk_oauth2_device_code_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `{$installer->getTable('admin/user')}` (`user_id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT `fk_oauth2_device_code_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `{$installer->getTable('customer/entity')}` (`entity_id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
    CONSTRAINT `fk_oauth2_device_code_client_id` FOREIGN KEY (`client_id`) REFERENCES `{$installer->getTable('oauth2/client')}` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB;
");

$installer->endSetup();
