-- Wires4 MySQL Database Schema Setup
-- Database: wirejybl_laravel_revamp

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `user_applications`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `pending_registrations`;
DROP TABLE IF EXISTS `password_reset_tokens`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Create table 'users'
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL,
    `login_id` VARCHAR(255) UNIQUE NULL,
    `wallet_address` VARCHAR(255) NULL,
    `network_type` VARCHAR(255) NULL,
    `status` VARCHAR(255) DEFAULT 'pending_review',
    `role` VARCHAR(255) DEFAULT 'user',
    `google2fa_secret` TEXT NULL,
    `google2fa_enabled` TINYINT DEFAULT 0,
    `sdm_selfie_link` TEXT NULL,
    `requested_documents` TEXT NULL,
    `buy_usdt_bank_name` TEXT NULL,
    `buy_usdt_bank_address` TEXT NULL,
    `buy_usdt_routing_no` TEXT NULL,
    `buy_usdt_account_no` TEXT NULL,
    `buy_usdt_beneficiary` TEXT NULL,
    `buy_usdt_bank_pdf` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create table 'pending_registrations'
CREATE TABLE `pending_registrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) UNIQUE NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create table 'password_reset_tokens'
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create table 'user_applications'
CREATE TABLE `user_applications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `account_type` VARCHAR(50) NULL,
    `current_step` VARCHAR(50) DEFAULT 'application_info',
    
    -- Individual onboarding columns
    `title_occupation` VARCHAR(255) NULL,
    `first_name` VARCHAR(255) NULL,
    `middle_name` VARCHAR(255) NULL,
    `last_name` VARCHAR(255) NULL,
    `dob` VARCHAR(100) NULL,
    `country` VARCHAR(255) NULL,
    `linkedin` VARCHAR(255) NULL,
    `instagram` VARCHAR(255) NULL,
    `twitter` VARCHAR(255) NULL,
    `street_address` VARCHAR(255) NULL,
    `unit_apartment` VARCHAR(255) NULL,
    `city` VARCHAR(255) NULL,
    `state_province` VARCHAR(255) NULL,
    `postal_zip` VARCHAR(100) NULL,
    `phone_number` VARCHAR(100) NULL,
    
    -- Trading Account columns
    `trading_purpose` VARCHAR(255) NULL,
    `trading_purpose_desc` TEXT NULL,
    `first_trade_date` VARCHAR(100) NULL,
    `flow_of_funds` VARCHAR(255) NULL,
    
    -- Financial details columns
    `first_trade_currency` VARCHAR(50) NULL,
    `first_trade_size` VARCHAR(100) NULL,
    `monthly_volume_currency` VARCHAR(50) NULL,
    `monthly_volume_size` VARCHAR(100) NULL,
    `source_funding` TEXT NULL,
    `annual_income_currency` VARCHAR(50) NULL,
    `annual_income_amount` VARCHAR(100) NULL,
    `liquid_assets_currency` VARCHAR(50) NULL,
    `liquid_assets_amount` VARCHAR(100) NULL,
    
    -- Risk Declarations columns
    `declared_bankruptcy` VARCHAR(50) NULL,
    `declared_bankruptcy_desc` TEXT NULL,
    `pep_status` VARCHAR(50) NULL,
    `pep_status_desc` TEXT NULL,
    `considerable_transactions` VARCHAR(50) NULL,
    `portfolio_excess` VARCHAR(50) NULL,
    
    -- Banking details columns
    `bank_currency` VARCHAR(50) NULL,
    `bank_account_holder` VARCHAR(255) NULL,
    `bank_account_number` VARCHAR(255) NULL,
    `bank_routing_code` VARCHAR(255) NULL,
    `bank_swift` VARCHAR(255) NULL,
    `bank_beneficiary_address` VARCHAR(255) NULL,
    `bank_name` VARCHAR(255) NULL,
    `bank_address` VARCHAR(255) NULL,
    `bank_country` VARCHAR(255) NULL,
    `bank_intermediary` VARCHAR(255) NULL,
    
    -- Proof of Funds files
    `proof_funds_type` VARCHAR(255) NULL,
    `proof_funds_description` TEXT NULL,
    `proof_funds_file` VARCHAR(255) NULL,
    
    -- Wallet configurations
    `wallet_address` VARCHAR(255) NULL,
    `network_type` VARCHAR(255) NULL,
    `declaration_signed` TINYINT DEFAULT 0,
    
    -- KYC details
    `kyc_document_type` VARCHAR(255) NULL,
    `kyc_document_file` VARCHAR(255) NULL,
    
    -- Referral
    `referral_source` VARCHAR(255) NULL,
    `referral_code` VARCHAR(255) NULL,
    
    -- Entity Verification columns
    `entity_type` VARCHAR(255) NULL,
    `lei_identifier` VARCHAR(255) NULL,
    `incorporation_country` VARCHAR(255) NULL,
    `incorporation_date` VARCHAR(100) NULL,
    `company_regulated` VARCHAR(50) NULL,
    `declared_bankruptcy_entity` VARCHAR(50) NULL,
    `declared_bankruptcy_entity_desc` TEXT NULL,
    `pep_status_entity` VARCHAR(50) NULL,
    `pep_status_entity_desc` TEXT NULL,
    `financial_entity_us` VARCHAR(50) NULL,
    `swap_dealer` VARCHAR(50) NULL,
    
    -- Entity Additional columns
    `company_name` VARCHAR(255) NULL,
    `company_reg_number` VARCHAR(255) NULL,
    `contact_number` VARCHAR(255) NULL,
    `source_funding_entity` VARCHAR(255) NULL,
    `nature_of_business` VARCHAR(255) NULL,
    `street_address_entity` VARCHAR(255) NULL,
    `country_entity` VARCHAR(255) NULL,
    `city_entity` VARCHAR(255) NULL,
    `state_entity` VARCHAR(255) NULL,
    `postal_entity` VARCHAR(255) NULL,
    `operating_address_different` VARCHAR(50) NULL,
    `has_website` VARCHAR(50) NULL,
    `website` VARCHAR(255) NULL,
    `linkedin_entity` VARCHAR(255) NULL,
    `instagram_entity` VARCHAR(255) NULL,
    `twitter_entity` VARCHAR(255) NULL,
    `accredited_investor` VARCHAR(50) NULL,
    
    -- Corporate Entity Upload Documents (Step 7)
    `entity_articles_file` VARCHAR(255) NULL,
    `entity_shareholders_file` VARCHAR(255) NULL,
    `entity_bank_statement_file` VARCHAR(255) NULL,
    `entity_proof_address_file` VARCHAR(255) NULL,
    `entity_board_resolution_file` VARCHAR(255) NULL,
    
    -- Serialized Dynamic Lists (Step 8 & 9)
    `entity_ubos_json` TEXT NULL,
    `entity_directors_json` TEXT NULL,
    `entity_authorized_signatories_json` TEXT NULL,
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Seed initial administrative and test profiles
-- Seeds default login credentials: password123
INSERT INTO `users` (`name`, `email`, `password`, `login_id`, `role`, `status`, `wallet_address`, `network_type`)
VALUES 
('System Administrator', 'admin@wiresforusdt.com', '$2y$12$R.H6R/5qP.LzPZlC/jPqOesGqH0oE1bYj0T45jK5d1V8G4U4K6K6K', 'admin', 'admin', 'active', '0x0000000000000000000000000000000000000000', 'ERC-20'),
('John Doe', 'john@example.com', '$2y$12$R.H6R/5qP.LzPZlC/jPqOesGqH0oE1bYj0T45jK5d1V8G4U4K6K6K', NULL, 'user', 'pending_review', '0x71C7656EC7ab88b098defB751B7401B5f6d8976F', 'TRC-20');
