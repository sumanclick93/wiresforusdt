<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class USDTSellRequest
{
    public ?int $id = null;
    public int $user_id;
    public float $usdt_amount;
    public string $platform_wallet_address;
    public string $bank_name;
    public string $bank_account_number;
    public string $bank_account_holder;
    public string $bank_swift;
    public string $status = 'pending';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    /**
     * Map database row object to USDTSellRequest instance.
     */
    public static function map($row): ?self
    {
        if (!$row) return null;

        $request = new self();
        $request->id = (int)$row->id;
        $request->user_id = (int)$row->user_id;
        $request->usdt_amount = (float)$row->usdt_amount;
        $request->platform_wallet_address = $row->platform_wallet_address;
        $request->bank_name = $row->bank_name;
        $request->bank_account_number = $row->bank_account_number;
        $request->bank_account_holder = $row->bank_account_holder;
        $request->bank_swift = $row->bank_swift;
        $request->status = $row->status;
        $request->created_at = $row->created_at;
        $request->updated_at = $row->updated_at;

        return $request;
    }

    /**
     * Find a purchase request by ID.
     */
    public static function find(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usdt_sell_requests WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::map($row) : null;
    }

    /**
     * Retrieve all sell requests submitted by a specific user.
     *
     * @return self[]
     */
    public static function findByUserId(int $userId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usdt_sell_requests WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll();

        $requests = [];
        foreach ($rows as $row) {
            $requests[] = self::map($row);
        }
        return $requests;
    }

    /**
     * Retrieve all pending requests for administrator review.
     *
     * @return self[]
     */
    public static function allPending(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM usdt_sell_requests WHERE status = 'pending' ORDER BY created_at DESC");
        $rows = $stmt->fetchAll();

        $requests = [];
        foreach ($rows as $row) {
            $requests[] = self::map($row);
        }
        return $requests;
    }

    /**
     * Retrieve all non-pending (approved/rejected) requests.
     *
     * @return self[]
     */
    public static function allHistory(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM usdt_sell_requests WHERE status != 'pending' ORDER BY updated_at DESC");
        $rows = $stmt->fetchAll();

        $requests = [];
        foreach ($rows as $row) {
            $requests[] = self::map($row);
        }
        return $requests;
    }

    /**
     * Save the USDT sell request record (Insert if new, Update if exists).
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
                INSERT INTO usdt_sell_requests (
                    user_id, usdt_amount, platform_wallet_address,
                    bank_name, bank_account_number, bank_account_holder, bank_swift,
                    status, created_at, updated_at
                ) VALUES (
                    :user_id, :usdt_amount, :platform_wallet_address,
                    :bank_name, :bank_account_number, :bank_account_holder, :bank_swift,
                    :status, :created_at, :updated_at
                )
            ");

            $result = $stmt->execute([
                ':user_id' => $this->user_id,
                ':usdt_amount' => $this->usdt_amount,
                ':platform_wallet_address' => $this->platform_wallet_address,
                ':bank_name' => $this->bank_name,
                ':bank_account_number' => $this->bank_account_number,
                ':bank_account_holder' => $this->bank_account_holder,
                ':bank_swift' => $this->bank_swift,
                ':status' => $this->status,
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
                UPDATE usdt_sell_requests SET
                    user_id = :user_id,
                    usdt_amount = :usdt_amount,
                    platform_wallet_address = :platform_wallet_address,
                    bank_name = :bank_name,
                    bank_account_number = :bank_account_number,
                    bank_account_holder = :bank_account_holder,
                    bank_swift = :bank_swift,
                    status = :status,
                    updated_at = :updated_at
                WHERE id = :id
            ");

            return $stmt->execute([
                ':id' => $this->id,
                ':user_id' => $this->user_id,
                ':usdt_amount' => $this->usdt_amount,
                ':platform_wallet_address' => $this->platform_wallet_address,
                ':bank_name' => $this->bank_name,
                ':bank_account_number' => $this->bank_account_number,
                ':bank_account_holder' => $this->bank_account_holder,
                ':bank_swift' => $this->bank_swift,
                ':status' => $this->status,
                ':updated_at' => $this->updated_at
            ]);
        }
    }
}
