<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public ?int $id = null;
    public string $name;
    public string $email;
    public ?string $email_verified_at = null;
    public string $password;
    public ?string $remember_token = null;
    public ?string $login_id = null;
    public ?string $wallet_address = null;
    public ?string $network_type = null;
    public string $status = 'pending_review';
    public string $role = 'user';
    public ?string $google2fa_secret = null;
    public bool $google2fa_enabled = false;
    public string $edit_permission_status = 'none';
    public ?string $pending_profile_update = null;
    public float $usdt_balance = 0.0;
    public ?string $sdm_selfie_link = null;
    public ?string $requested_documents = null;
    public ?string $buy_usdt_bank_name = null;
    public ?string $buy_usdt_bank_address = null;
    public ?string $buy_usdt_routing_no = null;
    public ?string $buy_usdt_account_no = null;
    public ?string $buy_usdt_beneficiary = null;
    public ?string $buy_usdt_bank_pdf = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    /**
     * Map database row object to User class instance.
     */
    public static function map($row): ?self
    {
        if (!$row) return null;

        $user = new self();
        $user->id = (int)$row->id;
        $user->name = $row->name;
        $user->email = $row->email;
        $user->email_verified_at = $row->email_verified_at;
        $user->password = $row->password;
        $user->remember_token = $row->remember_token;
        $user->login_id = $row->login_id;
        $user->wallet_address = $row->wallet_address;
        $user->network_type = $row->network_type;
        $user->status = $row->status;
        $user->role = $row->role;
        $user->google2fa_secret = $row->google2fa_secret;
        $user->google2fa_enabled = (bool)$row->google2fa_enabled;
        $user->edit_permission_status = $row->edit_permission_status ?? 'none';
        $user->pending_profile_update = $row->pending_profile_update ?? null;
        $user->usdt_balance = (float)($row->usdt_balance ?? 0.0);
        $user->sdm_selfie_link = $row->sdm_selfie_link ?? null;
        $user->requested_documents = $row->requested_documents ?? null;
        $user->buy_usdt_bank_name = $row->buy_usdt_bank_name ?? null;
        $user->buy_usdt_bank_address = $row->buy_usdt_bank_address ?? null;
        $user->buy_usdt_routing_no = $row->buy_usdt_routing_no ?? null;
        $user->buy_usdt_account_no = $row->buy_usdt_account_no ?? null;
        $user->buy_usdt_beneficiary = $row->buy_usdt_beneficiary ?? null;
        $user->buy_usdt_bank_pdf = $row->buy_usdt_bank_pdf ?? null;
        $user->created_at = $row->created_at;
        $user->updated_at = $row->updated_at;

        return $user;
    }

    /**
     * Find a user by primary key ID.
     */
    public static function find(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::map($row) : null;
    }

    /**
     * Find a user by Email address.
     */
    public static function findByEmail(string $email): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? self::map($row) : null;
    }

    /**
     * Find a user by Login ID.
     */
    public static function findByLoginId(string $loginId): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE login_id = :login_id LIMIT 1");
        $stmt->execute([':login_id' => $loginId]);
        $row = $stmt->fetch();
        return $row ? self::map($row) : null;
    }

    /**
     * Get all users in pending_review status.
     *
     * @return self[]
     */
    public static function allPending(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM users WHERE status = 'pending_review' ORDER BY created_at DESC");
        $rows = $stmt->fetchAll();
        
        $users = [];
        foreach ($rows as $row) {
            $users[] = self::map($row);
        }
        return $users;
    }

    /**
     * Get all active users (excluding admins).
     *
     * @return self[]
     */
    public static function allActiveUsers(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM users WHERE status = 'active' AND role = 'user' ORDER BY created_at DESC");
        $rows = $stmt->fetchAll();
        
        $users = [];
        foreach ($rows as $row) {
            $users[] = self::map($row);
        }
        return $users;
    }

    /**
     * Save the user record (Insert if new, Update if exists).
     */
    public function save(): bool
    {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $this->updated_at = $now;

        if ($this->id === null) {
            // INSERT
            $this->created_at = $now;
            $stmt = $db->prepare("
                INSERT INTO users (
                    name, email, email_verified_at, password, remember_token, 
                    login_id, wallet_address, network_type, status, role, 
                    google2fa_secret, google2fa_enabled, edit_permission_status, 
                    pending_profile_update, usdt_balance, sdm_selfie_link, requested_documents,
                    buy_usdt_bank_name, buy_usdt_bank_address, buy_usdt_routing_no, buy_usdt_account_no, buy_usdt_beneficiary, buy_usdt_bank_pdf,
                    created_at, updated_at
                ) VALUES (
                    :name, :email, :email_verified_at, :password, :remember_token, 
                    :login_id, :wallet_address, :network_type, :status, :role, 
                    :google2fa_secret, :google2fa_enabled, :edit_permission_status, 
                    :pending_profile_update, :usdt_balance, :sdm_selfie_link, :requested_documents,
                    :buy_usdt_bank_name, :buy_usdt_bank_address, :buy_usdt_routing_no, :buy_usdt_account_no, :buy_usdt_beneficiary, :buy_usdt_bank_pdf,
                    :created_at, :updated_at
                )
            ");
            
            $result = $stmt->execute([
                ':name' => $this->name,
                ':email' => $this->email,
                ':email_verified_at' => $this->email_verified_at,
                ':password' => $this->password,
                ':remember_token' => $this->remember_token,
                ':login_id' => $this->login_id,
                ':wallet_address' => $this->wallet_address,
                ':network_type' => $this->network_type,
                ':status' => $this->status,
                ':role' => $this->role,
                ':google2fa_secret' => $this->google2fa_secret,
                ':google2fa_enabled' => $this->google2fa_enabled ? 1 : 0,
                ':edit_permission_status' => $this->edit_permission_status,
                ':pending_profile_update' => $this->pending_profile_update,
                ':usdt_balance' => $this->usdt_balance,
                ':sdm_selfie_link' => $this->sdm_selfie_link,
                ':requested_documents' => $this->requested_documents,
                ':buy_usdt_bank_name' => $this->buy_usdt_bank_name,
                ':buy_usdt_bank_address' => $this->buy_usdt_bank_address,
                ':buy_usdt_routing_no' => $this->buy_usdt_routing_no,
                ':buy_usdt_account_no' => $this->buy_usdt_account_no,
                ':buy_usdt_beneficiary' => $this->buy_usdt_beneficiary,
                ':buy_usdt_bank_pdf' => $this->buy_usdt_bank_pdf,
                ':created_at' => $this->created_at,
                ':updated_at' => $this->updated_at
            ]);

            if ($result) {
                $this->id = (int)$db->lastInsertId();
            }
            return $result;
        } else {
            // UPDATE
            $stmt = $db->prepare("
                UPDATE users SET 
                    name = :name,
                    email = :email,
                    email_verified_at = :email_verified_at,
                    password = :password,
                    remember_token = :remember_token,
                    login_id = :login_id,
                    wallet_address = :wallet_address,
                    network_type = :network_type,
                    status = :status,
                    role = :role,
                    google2fa_secret = :google2fa_secret,
                    google2fa_enabled = :google2fa_enabled,
                    edit_permission_status = :edit_permission_status,
                    pending_profile_update = :pending_profile_update,
                    usdt_balance = :usdt_balance,
                    sdm_selfie_link = :sdm_selfie_link,
                    requested_documents = :requested_documents,
                    buy_usdt_bank_name = :buy_usdt_bank_name,
                    buy_usdt_bank_address = :buy_usdt_bank_address,
                    buy_usdt_routing_no = :buy_usdt_routing_no,
                    buy_usdt_account_no = :buy_usdt_account_no,
                    buy_usdt_beneficiary = :buy_usdt_beneficiary,
                    buy_usdt_bank_pdf = :buy_usdt_bank_pdf,
                    updated_at = :updated_at
                WHERE id = :id
            ");

            return $stmt->execute([
                ':id' => $this->id,
                ':name' => $this->name,
                ':email' => $this->email,
                ':email_verified_at' => $this->email_verified_at,
                ':password' => $this->password,
                ':remember_token' => $this->remember_token,
                ':login_id' => $this->login_id,
                ':wallet_address' => $this->wallet_address,
                ':network_type' => $this->network_type,
                ':status' => $this->status,
                ':role' => $this->role,
                ':google2fa_secret' => $this->google2fa_secret,
                ':google2fa_enabled' => $this->google2fa_enabled ? 1 : 0,
                ':edit_permission_status' => $this->edit_permission_status,
                ':pending_profile_update' => $this->pending_profile_update,
                ':usdt_balance' => $this->usdt_balance,
                ':sdm_selfie_link' => $this->sdm_selfie_link,
                ':requested_documents' => $this->requested_documents,
                ':buy_usdt_bank_name' => $this->buy_usdt_bank_name,
                ':buy_usdt_bank_address' => $this->buy_usdt_bank_address,
                ':buy_usdt_routing_no' => $this->buy_usdt_routing_no,
                ':buy_usdt_account_no' => $this->buy_usdt_account_no,
                ':buy_usdt_beneficiary' => $this->buy_usdt_beneficiary,
                ':buy_usdt_bank_pdf' => $this->buy_usdt_bank_pdf,
                ':updated_at' => $this->updated_at
            ]);
        }
    }
}
