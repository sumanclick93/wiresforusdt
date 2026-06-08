<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    /**
     * Get active PDO database connection instance (Singleton).
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $connection = Config::get('DB_CONNECTION', 'sqlite');

            try {
                if ($connection === 'sqlite') {
                    // Get sqlite database path
                    $dbPath = Config::get('DB_DATABASE', __DIR__ . '/../../database/database.sqlite');
                    
                    // Create directory if missing
                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    if (!file_exists($dbPath)) {
                        touch($dbPath);
                    }

                    self::$instance = new PDO("sqlite:$dbPath");
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Enable WAL mode, set a 5-second busy timeout, and enable foreign keys (eliminates locks)
                    self::$instance->exec('PRAGMA journal_mode = WAL;');
                    self::$instance->exec('PRAGMA busy_timeout = 5000;');
                    self::$instance->exec('PRAGMA foreign_keys = ON;');
                } else {
                    // MySQL connection
                    $host = Config::get('DB_HOST', '127.0.0.1');
                    $port = Config::get('DB_PORT', '3306');
                    $db = Config::get('DB_DATABASE', 'laravel');
                    $user = Config::get('DB_USERNAME', 'root');
                    $pass = Config::get('DB_PASSWORD', '');

                    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
                    self::$instance = new PDO($dsn, $user, $pass);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }

                // Bootstrap tables and seed data if missing
                self::bootstrapDatabase(self::$instance, $connection);

            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Bootstrap tables and seed initial administrator/test profiles.
     */
    private static function bootstrapDatabase(PDO $pdo, string $driver): void
    {
        // Check and create dropdown_options table if missing, then seed defaults
        try {
            if ($driver === 'sqlite') {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS dropdown_options (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        dropdown_key VARCHAR(255) NOT NULL,
                        option_value VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    );
                ");
            } else {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS dropdown_options (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        dropdown_key VARCHAR(255) NOT NULL,
                        option_value VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
            }

            // Seed initial defaults if table is empty or update missing options
            $count = (int)$pdo->query("SELECT COUNT(*) FROM dropdown_options")->fetchColumn();
            $defaults = [
                'countries' => ['United States', 'Canada', 'United Kingdom', 'Germany', 'Czech Republic', 'Singapore', 'Hong Kong', 'Austria', 'Belgium', 'Bulgaria', 'Croatia', 'Cyprus', 'Denmark', 'Estonia', 'Finland', 'France', 'Greece', 'Hungary', 'Iceland', 'Ireland', 'Italy', 'Latvia', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Norway', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden', 'Mexico', 'El Salvador', 'Panama', 'Bahamas', 'Cayman Islands', 'Bermuda', 'BVI', 'Costa Rica', 'Guatemala', 'Belize', 'Brazil', 'Argentina', 'Colombia', 'Chile', 'Peru', 'Uruguay', 'Paraguay', 'Japan', 'South Korea', 'Philippines', 'Thailand', 'Indonesia', 'Malaysia', 'Taiwan', 'Kazakhstan', 'Bahrain', 'Saudi Arabia', 'Algeria', 'Angola', 'Benin', 'Botswana', 'Burkina Faso', 'Burundi', 'Cameroon', 'Cape Verde', 'Central African Republic', 'Chad', 'Comoros', 'Democratic Republic of the Congo', 'Republic of the Congo', 'Djibouti', 'Egypt', 'Equatorial Guinea', 'Eritrea', 'Eswatini', 'Ethiopia', 'Gabon', 'Gambia', 'Ghana', 'Guinea', 'Guinea-Bissau', 'Ivory Coast', 'Kenya', 'Lesotho', 'Liberia', 'Libya', 'Madagascar', 'Malawi', 'Mali', 'Mauritania', 'Mauritius', 'Morocco', 'Mozambique', 'Namibia', 'Niger', 'Nigeria', 'Rwanda', 'Sao Tome and Principe', 'Senegal', 'Seychelles', 'Sierra Leone', 'Somalia', 'South Africa', 'South Sudan', 'Sudan', 'Tanzania', 'Togo', 'Tunisia', 'Uganda', 'Zambia', 'Zimbabwe'],
                'entity_types' => ['Corporation', 'Limited Liability Company', 'Partnership', 'Sole Proprietorship', 'Trust', 'Other'],
                'funding_sources' => ['Retained Earnings', 'Equity Capital', 'Debt financing', 'Investor capital', 'Other'],
                'business_natures' => ['Asset Management', 'Proprietary Trading', 'Venture Capital', 'Crypto Exchange / Brokerage', 'Family Office', 'Other'],
                'occupations' => ['Chief Executive Officer', 'Managing Director', 'Portfolio Manager', 'Compliance Officer', 'Trader', 'Other'],
                'trading_purposes' => ['Speculation', 'Hedging', 'Liquidity Provision', 'Long-term investment', 'Other'],
                'funds_flows' => ['Incoming Bank Wire', 'Outgoing Bank Wire', 'USDT/Digital Settlement', 'Bilateral OTC Clearing', 'Other'],
                'currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'SGD', 'HKD'],
                'network_types' => ['ERC-20', 'TRC-20', 'Solana', 'Arbitrum', 'Optimism'],
                'referral_sources' => ['Google Search', 'Telegram Channels', 'Institutional Broker', 'Word of Mouth', 'Other']
            ];

            if ($count === 0) {
                $stmt = $pdo->prepare("INSERT INTO dropdown_options (dropdown_key, option_value) VALUES (:dropdown_key, :option_value)");
                foreach ($defaults as $key => $values) {
                    foreach ($values as $val) {
                        $stmt->execute([':dropdown_key' => $key, ':option_value' => $val]);
                    }
                }
            } else {
                // Ensure all default countries are present in the database (migration check)
                $existing = $pdo->query("SELECT option_value FROM dropdown_options WHERE dropdown_key = 'countries'")->fetchAll(PDO::FETCH_COLUMN) ?: [];
                $existing_lower = array_map('strtolower', $existing);
                
                $stmt = $pdo->prepare("INSERT INTO dropdown_options (dropdown_key, option_value) VALUES ('countries', :option_value)");
                foreach ($defaults['countries'] as $country) {
                    if (!in_array(strtolower($country), $existing_lower)) {
                        $stmt->execute([':option_value' => $country]);
                    }
                }
            }
        } catch (\PDOException $e) {
            // Ignore migration error
        }

        // Check if users table already exists
        $tableExists = false;
        try {
            if ($driver === 'sqlite') {
                $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
                $tableExists = ($result->fetch() !== false);
                $result->closeCursor();
                $result = null;
            } else {
                $result = $pdo->query("SHOW TABLES LIKE 'users'");
                $tableExists = ($result->fetch() !== false);
                $result->closeCursor();
                $result = null;
            }
        } catch (PDOException $e) {
            $tableExists = false;
        }

        // Check and create user_applications table if missing
        $appTableExists = false;
        try {
            if ($driver === 'sqlite') {
                $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='user_applications'");
                $appTableExists = ($result->fetch() !== false);
                $result->closeCursor();
                $result = null;
            } else {
                $result = $pdo->query("SHOW TABLES LIKE 'user_applications'");
                $appTableExists = ($result->fetch() !== false);
                $result->closeCursor();
                $result = null;
            }
        } catch (PDOException $e) {
            $appTableExists = false;
        }

        // Check if user_applications table has entity_articles_file column (migration check for Entity onboarding)
        $hasNewColumns = false;
        if ($appTableExists) {
            try {
                if ($driver === 'sqlite') {
                    $stmt = $pdo->query("PRAGMA table_info(user_applications)");
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    $stmt = null;
                    foreach ($columns as $column) {
                        if ($column['name'] === 'entity_articles_file') {
                            $hasNewColumns = true;
                            break;
                        }
                    }
                } else {
                    $stmt = $pdo->query("SHOW COLUMNS FROM user_applications LIKE 'entity_articles_file'");
                    $hasNewColumns = ($stmt->fetch() !== false);
                    $stmt->closeCursor();
                    $stmt = null;
                }
            } catch (PDOException $e) {
                $hasNewColumns = false;
            }
        }

        if ($appTableExists && !$hasNewColumns) {
            $pdo->exec("DROP TABLE IF EXISTS user_applications");
            $appTableExists = false;
        }

        if (!$appTableExists) {
            if ($driver === 'sqlite') {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS user_applications (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        email VARCHAR(255) UNIQUE NOT NULL,
                        account_type VARCHAR(50) NULL,
                        current_step VARCHAR(50) DEFAULT 'application_info',
                        title_occupation VARCHAR(255) NULL,
                        first_name VARCHAR(255) NULL,
                        middle_name VARCHAR(255) NULL,
                        last_name VARCHAR(255) NULL,
                        dob VARCHAR(100) NULL,
                        country VARCHAR(255) NULL,
                        linkedin VARCHAR(255) NULL,
                        instagram VARCHAR(255) NULL,
                        twitter VARCHAR(255) NULL,
                        street_address VARCHAR(255) NULL,
                        unit_apartment VARCHAR(255) NULL,
                        city VARCHAR(255) NULL,
                        state_province VARCHAR(255) NULL,
                        postal_zip VARCHAR(100) NULL,
                        phone_number VARCHAR(100) NULL,
                        trading_purpose VARCHAR(255) NULL,
                        trading_purpose_desc TEXT NULL,
                        first_trade_date VARCHAR(100) NULL,
                        flow_of_funds VARCHAR(255) NULL,
                        first_trade_currency VARCHAR(50) NULL,
                        first_trade_size VARCHAR(100) NULL,
                        monthly_volume_currency VARCHAR(50) NULL,
                        monthly_volume_size VARCHAR(100) NULL,
                        source_funding TEXT NULL,
                        annual_income_currency VARCHAR(50) NULL,
                        annual_income_amount VARCHAR(100) NULL,
                        liquid_assets_currency VARCHAR(50) NULL,
                        liquid_assets_amount VARCHAR(100) NULL,
                        declared_bankruptcy VARCHAR(50) NULL,
                        declared_bankruptcy_desc TEXT NULL,
                        pep_status VARCHAR(50) NULL,
                        pep_status_desc TEXT NULL,
                        considerable_transactions VARCHAR(50) NULL,
                        portfolio_excess VARCHAR(50) NULL,
                        bank_currency VARCHAR(50) NULL,
                        bank_account_holder VARCHAR(255) NULL,
                        bank_account_number VARCHAR(255) NULL,
                        bank_routing_code VARCHAR(255) NULL,
                        bank_swift VARCHAR(255) NULL,
                        bank_beneficiary_address VARCHAR(255) NULL,
                        bank_name VARCHAR(255) NULL,
                        bank_address VARCHAR(255) NULL,
                        bank_country VARCHAR(255) NULL,
                        bank_intermediary VARCHAR(255) NULL,
                        proof_funds_type VARCHAR(255) NULL,
                        proof_funds_description TEXT NULL,
                        proof_funds_file VARCHAR(255) NULL,
                        wallet_address VARCHAR(255) NULL,
                        network_type VARCHAR(255) NULL,
                        declaration_signed BOOLEAN DEFAULT 0,
                        kyc_document_type VARCHAR(255) NULL,
                        kyc_document_file VARCHAR(255) NULL,
                        referral_source VARCHAR(255) NULL,
                        referral_code VARCHAR(255) NULL,
                        entity_type VARCHAR(255) NULL,
                        lei_identifier VARCHAR(255) NULL,
                        incorporation_country VARCHAR(255) NULL,
                        incorporation_date VARCHAR(100) NULL,
                        company_regulated VARCHAR(50) NULL,
                        declared_bankruptcy_entity VARCHAR(50) NULL,
                        declared_bankruptcy_entity_desc TEXT NULL,
                        pep_status_entity VARCHAR(50) NULL,
                        pep_status_entity_desc TEXT NULL,
                        financial_entity_us VARCHAR(50) NULL,
                        swap_dealer VARCHAR(50) NULL,
                        company_name VARCHAR(255) NULL,
                        company_reg_number VARCHAR(255) NULL,
                        contact_number VARCHAR(255) NULL,
                        source_funding_entity VARCHAR(255) NULL,
                        nature_of_business VARCHAR(255) NULL,
                        street_address_entity VARCHAR(255) NULL,
                        country_entity VARCHAR(255) NULL,
                        city_entity VARCHAR(255) NULL,
                        state_entity VARCHAR(255) NULL,
                        postal_entity VARCHAR(255) NULL,
                        operating_address_different VARCHAR(50) NULL,
                        has_website VARCHAR(50) NULL,
                        website VARCHAR(255) NULL,
                        linkedin_entity VARCHAR(255) NULL,
                        instagram_entity VARCHAR(255) NULL,
                        twitter_entity VARCHAR(255) NULL,
                        accredited_investor VARCHAR(50) NULL,
                        entity_articles_file VARCHAR(255) NULL,
                        entity_shareholders_file VARCHAR(255) NULL,
                        entity_bank_statement_file VARCHAR(255) NULL,
                        entity_proof_address_file VARCHAR(255) NULL,
                        entity_board_resolution_file VARCHAR(255) NULL,
                        entity_ubos_json TEXT NULL,
                        entity_directors_json TEXT NULL,
                        entity_authorized_signatories_json TEXT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    );
                ");
            } else {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS user_applications (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(255) UNIQUE NOT NULL,
                        account_type VARCHAR(50) NULL,
                        current_step VARCHAR(50) DEFAULT 'application_info',
                        title_occupation VARCHAR(255) NULL,
                        first_name VARCHAR(255) NULL,
                        middle_name VARCHAR(255) NULL,
                        last_name VARCHAR(255) NULL,
                        dob VARCHAR(100) NULL,
                        country VARCHAR(255) NULL,
                        linkedin VARCHAR(255) NULL,
                        instagram VARCHAR(255) NULL,
                        twitter VARCHAR(255) NULL,
                        street_address VARCHAR(255) NULL,
                        unit_apartment VARCHAR(255) NULL,
                        city VARCHAR(255) NULL,
                        state_province VARCHAR(255) NULL,
                        postal_zip VARCHAR(100) NULL,
                        phone_number VARCHAR(100) NULL,
                        trading_purpose VARCHAR(255) NULL,
                        trading_purpose_desc TEXT NULL,
                        first_trade_date VARCHAR(100) NULL,
                        flow_of_funds VARCHAR(255) NULL,
                        first_trade_currency VARCHAR(50) NULL,
                        first_trade_size VARCHAR(100) NULL,
                        monthly_volume_currency VARCHAR(50) NULL,
                        monthly_volume_size VARCHAR(100) NULL,
                        source_funding TEXT NULL,
                        annual_income_currency VARCHAR(50) NULL,
                        annual_income_amount VARCHAR(100) NULL,
                        liquid_assets_currency VARCHAR(50) NULL,
                        liquid_assets_amount VARCHAR(100) NULL,
                        declared_bankruptcy VARCHAR(50) NULL,
                        declared_bankruptcy_desc TEXT NULL,
                        pep_status VARCHAR(50) NULL,
                        pep_status_desc TEXT NULL,
                        considerable_transactions VARCHAR(50) NULL,
                        portfolio_excess VARCHAR(50) NULL,
                        bank_currency VARCHAR(50) NULL,
                        bank_account_holder VARCHAR(255) NULL,
                        bank_account_number VARCHAR(255) NULL,
                        bank_routing_code VARCHAR(255) NULL,
                        bank_swift VARCHAR(255) NULL,
                        bank_beneficiary_address VARCHAR(255) NULL,
                        bank_name VARCHAR(255) NULL,
                        bank_address VARCHAR(255) NULL,
                        bank_country VARCHAR(255) NULL,
                        bank_intermediary VARCHAR(255) NULL,
                        proof_funds_type VARCHAR(255) NULL,
                        proof_funds_description TEXT NULL,
                        proof_funds_file VARCHAR(255) NULL,
                        wallet_address VARCHAR(255) NULL,
                        network_type VARCHAR(255) NULL,
                        declaration_signed TINYINT DEFAULT 0,
                        kyc_document_type VARCHAR(255) NULL,
                        kyc_document_file VARCHAR(255) NULL,
                        referral_source VARCHAR(255) NULL,
                        referral_code VARCHAR(255) NULL,
                        entity_type VARCHAR(255) NULL,
                        lei_identifier VARCHAR(255) NULL,
                        incorporation_country VARCHAR(255) NULL,
                        incorporation_date VARCHAR(100) NULL,
                        company_regulated VARCHAR(50) NULL,
                        declared_bankruptcy_entity VARCHAR(50) NULL,
                        declared_bankruptcy_entity_desc TEXT NULL,
                        pep_status_entity VARCHAR(50) NULL,
                        pep_status_entity_desc TEXT NULL,
                        financial_entity_us VARCHAR(50) NULL,
                        swap_dealer VARCHAR(50) NULL,
                        company_name VARCHAR(255) NULL,
                        company_reg_number VARCHAR(255) NULL,
                        contact_number VARCHAR(255) NULL,
                        source_funding_entity VARCHAR(255) NULL,
                        nature_of_business VARCHAR(255) NULL,
                        street_address_entity VARCHAR(255) NULL,
                        country_entity VARCHAR(255) NULL,
                        city_entity VARCHAR(255) NULL,
                        state_entity VARCHAR(255) NULL,
                        postal_entity VARCHAR(255) NULL,
                        operating_address_different VARCHAR(50) NULL,
                        has_website VARCHAR(50) NULL,
                        website VARCHAR(255) NULL,
                        linkedin_entity VARCHAR(255) NULL,
                        instagram_entity VARCHAR(255) NULL,
                        twitter_entity VARCHAR(255) NULL,
                        accredited_investor VARCHAR(50) NULL,
                        entity_articles_file VARCHAR(255) NULL,
                        entity_shareholders_file VARCHAR(255) NULL,
                        entity_bank_statement_file VARCHAR(255) NULL,
                        entity_proof_address_file VARCHAR(255) NULL,
                        entity_board_resolution_file VARCHAR(255) NULL,
                        entity_ubos_json TEXT NULL,
                        entity_directors_json TEXT NULL,
                        entity_authorized_signatories_json TEXT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
            }
        }

        // Migrate existing legacy Entity/Individual values to corporate/individual in database
        try {
            $pdo->exec("UPDATE user_applications SET account_type = 'corporate' WHERE account_type = 'Entity'");
            $pdo->exec("UPDATE user_applications SET account_type = 'individual' WHERE account_type = 'Individual'");
        } catch (\PDOException $e) {
            // Ignore
        }

        if ($tableExists) {
            // Check if edit_permission_status exists, if not, add it and pending_profile_update
            $hasEditStatus = false;
            try {
                if ($driver === 'sqlite') {
                    $stmt = $pdo->query("PRAGMA table_info(users)");
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    foreach ($columns as $column) {
                        if ($column['name'] === 'edit_permission_status') {
                            $hasEditStatus = true;
                            break;
                        }
                    }
                } else {
                    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'edit_permission_status'");
                    $hasEditStatus = ($stmt->fetch() !== false);
                    $stmt->closeCursor();
                }
            } catch (\PDOException $e) {
                $hasEditStatus = false;
            }

            if (!$hasEditStatus) {
                try {
                    if ($driver === 'sqlite') {
                        $pdo->exec("ALTER TABLE users ADD COLUMN edit_permission_status VARCHAR(255) DEFAULT 'none'");
                        $pdo->exec("ALTER TABLE users ADD COLUMN pending_profile_update TEXT NULL");
                    } else {
                        $pdo->exec("ALTER TABLE users ADD COLUMN edit_permission_status VARCHAR(255) DEFAULT 'none'");
                        $pdo->exec("ALTER TABLE users ADD COLUMN pending_profile_update LONGTEXT NULL");
                    }
                } catch (\PDOException $e) {
                    // Ignore migration issues if columns already exist or fail
                }
            }

            // Check if usdt_balance exists, if not, add it
            $hasUsdtBalance = false;
            try {
                if ($driver === 'sqlite') {
                    $stmt = $pdo->query("PRAGMA table_info(users)");
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    foreach ($columns as $column) {
                        if ($column['name'] === 'usdt_balance') {
                            $hasUsdtBalance = true;
                            break;
                        }
                    }
                } else {
                    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'usdt_balance'");
                    $hasUsdtBalance = ($stmt->fetch() !== false);
                    $stmt->closeCursor();
                }
            } catch (\PDOException $e) {
                $hasUsdtBalance = false;
            }

            if (!$hasUsdtBalance) {
                try {
                    $pdo->exec("ALTER TABLE users ADD COLUMN usdt_balance DECIMAL(16,6) DEFAULT 0.000000");
                } catch (\PDOException $e) {
                    // Ignore
                }
            }

            // Check if sdm_selfie_link exists, if not, add it
            $hasSdmSelfie = false;
            try {
                if ($driver === 'sqlite') {
                    $stmt = $pdo->query("PRAGMA table_info(users)");
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    foreach ($columns as $column) {
                        if ($column['name'] === 'sdm_selfie_link') {
                            $hasSdmSelfie = true;
                            break;
                        }
                    }
                } else {
                    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'sdm_selfie_link'");
                    $hasSdmSelfie = ($stmt->fetch() !== false);
                    $stmt->closeCursor();
                }
            } catch (\PDOException $e) {
                $hasSdmSelfie = false;
            }

            if (!$hasSdmSelfie) {
                try {
                    $pdo->exec("ALTER TABLE users ADD COLUMN sdm_selfie_link TEXT NULL");
                } catch (\PDOException $e) {
                    // Ignore
                }
            }

            // Check if requested_documents exists, if not, add it
            $hasRequestedDocs = false;
            try {
                if ($driver === 'sqlite') {
                    $stmt = $pdo->query("PRAGMA table_info(users)");
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    foreach ($columns as $column) {
                        if ($column['name'] === 'requested_documents') {
                            $hasRequestedDocs = true;
                            break;
                        }
                    }
                } else {
                    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'requested_documents'");
                    $hasRequestedDocs = ($stmt->fetch() !== false);
                    $stmt->closeCursor();
                }
            } catch (\PDOException $e) {
                $hasRequestedDocs = false;
            }

            // Add Buy USDT banking details columns to users table if missing
            $columnsToCheck = [
                'requested_documents' => 'TEXT NULL',
                'buy_usdt_bank_name' => 'TEXT NULL',
                'buy_usdt_bank_address' => 'TEXT NULL',
                'buy_usdt_routing_no' => 'TEXT NULL',
                'buy_usdt_account_no' => 'TEXT NULL',
                'buy_usdt_beneficiary' => 'TEXT NULL',
                'buy_usdt_bank_pdf' => 'TEXT NULL'
            ];

            foreach ($columnsToCheck as $colName => $colType) {
                $hasCol = false;
                try {
                    if ($driver === 'sqlite') {
                        $stmt = $pdo->query("PRAGMA table_info(users)");
                        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $stmt->closeCursor();
                        foreach ($columns as $column) {
                            if ($column['name'] === $colName) {
                                $hasCol = true;
                                break;
                            }
                        }
                    } else {
                        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE '{$colName}'");
                        $hasCol = ($stmt->fetch() !== false);
                        $stmt->closeCursor();
                    }
                } catch (\PDOException $e) {
                    $hasCol = false;
                }

                if (!$hasCol) {
                    try {
                        $pdo->exec("ALTER TABLE users ADD COLUMN {$colName} {$colType}");
                    } catch (\PDOException $e) {
                        // Ignore
                    }
                }
            }

            // Create usdt_purchase_requests table if missing
            try {
                if ($driver === 'sqlite') {
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS usdt_purchase_requests (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            user_id INTEGER NOT NULL,
                            receiving_bank_name VARCHAR(255) NOT NULL,
                            receiving_bank_address VARCHAR(255) NOT NULL,
                            routing_no_aba VARCHAR(255) NOT NULL,
                            beneficiary_account_number VARCHAR(255) NOT NULL,
                            beneficiary_name VARCHAR(255) NOT NULL,
                            deposit_reference_number VARCHAR(255) NOT NULL,
                            usdt_amount DECIMAL(16,6) NOT NULL,
                            proof_of_deposit VARCHAR(255) NOT NULL,
                            status VARCHAR(50) DEFAULT 'pending',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        );
                    ");
                } else {
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS usdt_purchase_requests (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            user_id INT NOT NULL,
                            receiving_bank_name VARCHAR(255) NOT NULL,
                            receiving_bank_address VARCHAR(255) NOT NULL,
                            routing_no_aba VARCHAR(255) NOT NULL,
                            beneficiary_account_number VARCHAR(255) NOT NULL,
                            beneficiary_name VARCHAR(255) NOT NULL,
                            deposit_reference_number VARCHAR(255) NOT NULL,
                            usdt_amount DECIMAL(16,6) NOT NULL,
                            proof_of_deposit VARCHAR(255) NOT NULL,
                            status VARCHAR(50) DEFAULT 'pending',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ");
                }
            } catch (\PDOException $e) {
                // Ignore
            }

            // Create usdt_sell_requests table if missing
            try {
                if ($driver === 'sqlite') {
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS usdt_sell_requests (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            user_id INTEGER NOT NULL,
                            usdt_amount DECIMAL(16,6) NOT NULL,
                            platform_wallet_address VARCHAR(255) NOT NULL,
                            bank_name VARCHAR(255) NOT NULL,
                            bank_account_number VARCHAR(255) NOT NULL,
                            bank_account_holder VARCHAR(255) NOT NULL,
                            bank_swift VARCHAR(255) NOT NULL,
                            status VARCHAR(50) DEFAULT 'pending',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        );
                    ");
                } else {
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS usdt_sell_requests (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            user_id INT NOT NULL,
                            usdt_amount DECIMAL(16,6) NOT NULL,
                            platform_wallet_address VARCHAR(255) NOT NULL,
                            bank_name VARCHAR(255) NOT NULL,
                            bank_account_number VARCHAR(255) NOT NULL,
                            bank_account_holder VARCHAR(255) NOT NULL,
                            bank_swift VARCHAR(255) NOT NULL,
                            status VARCHAR(50) DEFAULT 'pending',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ");
                }
            } catch (\PDOException $e) {
                // Ignore
            }

            return;
        }

        // Initialize SQLite Tables
        if ($driver === 'sqlite') {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    email_verified_at TIMESTAMP NULL,
                    password VARCHAR(255) NOT NULL,
                    remember_token VARCHAR(100) NULL,
                    login_id VARCHAR(255) UNIQUE NULL,
                    wallet_address VARCHAR(255) NULL,
                    network_type VARCHAR(255) NULL,
                    status VARCHAR(255) DEFAULT 'pending_review',
                    role VARCHAR(255) DEFAULT 'user',
                    google2fa_secret TEXT NULL,
                    google2fa_enabled BOOLEAN DEFAULT 0,
                    edit_permission_status VARCHAR(255) DEFAULT 'none',
                    pending_profile_update TEXT NULL,
                    usdt_balance DECIMAL(16,6) DEFAULT 0.000000,
                    sdm_selfie_link TEXT NULL,
                    requested_documents TEXT NULL,
                    buy_usdt_bank_name TEXT NULL,
                    buy_usdt_bank_address TEXT NULL,
                    buy_usdt_routing_no TEXT NULL,
                    buy_usdt_account_no TEXT NULL,
                    buy_usdt_beneficiary TEXT NULL,
                    buy_usdt_bank_pdf TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS pending_registrations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    email VARCHAR(255) NOT NULL,
                    token VARCHAR(255) UNIQUE NOT NULL,
                    expires_at TIMESTAMP NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS password_reset_tokens (
                    email VARCHAR(255) PRIMARY KEY,
                    token VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP NULL
                );

                CREATE TABLE IF NOT EXISTS usdt_purchase_requests (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    receiving_bank_name VARCHAR(255) NOT NULL,
                    receiving_bank_address VARCHAR(255) NOT NULL,
                    routing_no_aba VARCHAR(255) NOT NULL,
                    beneficiary_account_number VARCHAR(255) NOT NULL,
                    beneficiary_name VARCHAR(255) NOT NULL,
                    deposit_reference_number VARCHAR(255) NOT NULL,
                    usdt_amount DECIMAL(16,6) NOT NULL,
                    proof_of_deposit VARCHAR(255) NOT NULL,
                    status VARCHAR(50) DEFAULT 'pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS usdt_sell_requests (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    usdt_amount DECIMAL(16,6) NOT NULL,
                    platform_wallet_address VARCHAR(255) NOT NULL,
                    bank_name VARCHAR(255) NOT NULL,
                    bank_account_number VARCHAR(255) NOT NULL,
                    bank_account_holder VARCHAR(255) NOT NULL,
                    bank_swift VARCHAR(255) NOT NULL,
                    status VARCHAR(50) DEFAULT 'pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            ");
        } else {
            // Initialize MySQL Tables
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    email_verified_at TIMESTAMP NULL,
                    password VARCHAR(255) NOT NULL,
                    remember_token VARCHAR(100) NULL,
                    login_id VARCHAR(255) UNIQUE NULL,
                    wallet_address VARCHAR(255) NULL,
                    network_type VARCHAR(255) NULL,
                    status VARCHAR(255) DEFAULT 'pending_review',
                    role VARCHAR(255) DEFAULT 'user',
                    google2fa_secret TEXT NULL,
                    google2fa_enabled BOOLEAN DEFAULT 0,
                    edit_permission_status VARCHAR(255) DEFAULT 'none',
                    pending_profile_update LONGTEXT NULL,
                    usdt_balance DECIMAL(16,6) DEFAULT 0.000000,
                    sdm_selfie_link TEXT NULL,
                    requested_documents TEXT NULL,
                    buy_usdt_bank_name TEXT NULL,
                    buy_usdt_bank_address TEXT NULL,
                    buy_usdt_routing_no TEXT NULL,
                    buy_usdt_account_no TEXT NULL,
                    buy_usdt_beneficiary TEXT NULL,
                    buy_usdt_bank_pdf TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                CREATE TABLE IF NOT EXISTS pending_registrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL,
                    token VARCHAR(255) UNIQUE NOT NULL,
                    expires_at TIMESTAMP NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                CREATE TABLE IF NOT EXISTS password_reset_tokens (
                    email VARCHAR(255) PRIMARY KEY,
                    token VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                CREATE TABLE IF NOT EXISTS usdt_purchase_requests (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    receiving_bank_name VARCHAR(255) NOT NULL,
                    receiving_bank_address VARCHAR(255) NOT NULL,
                    routing_no_aba VARCHAR(255) NOT NULL,
                    beneficiary_account_number VARCHAR(255) NOT NULL,
                    beneficiary_name VARCHAR(255) NOT NULL,
                    deposit_reference_number VARCHAR(255) NOT NULL,
                    usdt_amount DECIMAL(16,6) NOT NULL,
                    proof_of_deposit VARCHAR(255) NOT NULL,
                    status VARCHAR(50) DEFAULT 'pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                CREATE TABLE IF NOT EXISTS usdt_sell_requests (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    usdt_amount DECIMAL(16,6) NOT NULL,
                    platform_wallet_address VARCHAR(255) NOT NULL,
                    bank_name VARCHAR(255) NOT NULL,
                    bank_account_number VARCHAR(255) NOT NULL,
                    bank_account_holder VARCHAR(255) NOT NULL,
                    bank_swift VARCHAR(255) NOT NULL,
                    status VARCHAR(50) DEFAULT 'pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // Seed initial admin and standard user profiles
        $adminPassword = password_hash('password123', PASSWORD_BCRYPT);
        $userPassword = password_hash('password123', PASSWORD_BCRYPT);

        // System Administrator
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, login_id, role, status, wallet_address, network_type) 
            VALUES (:name, :email, :password, :login_id, :role, :status, :wallet_address, :network_type)
        ");
        $stmt->execute([
            ':name' => 'System Administrator',
            ':email' => 'admin@wiresforusdt.com',
            ':password' => $adminPassword,
            ':login_id' => 'admin',
            ':role' => 'admin',
            ':status' => 'active',
            ':wallet_address' => '0x0000000000000000000000000000000000000000',
            ':network_type' => 'ERC-20',
        ]);

        // John Doe (standard user in pending_review)
        $stmt->execute([
            ':name' => 'John Doe',
            ':email' => 'john@example.com',
            ':password' => $userPassword,
            ':login_id' => null,
            ':role' => 'user',
            ':status' => 'pending_review',
            ':wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
            ':network_type' => 'TRC-20',
        ]);
    }
}
