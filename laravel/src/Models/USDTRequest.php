<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class USDTRequest
{
    public ?int $id = null;
    public int $user_id;
    public string $receiving_bank_name;
    public string $receiving_bank_address;
    public string $routing_no_aba;
    public string $beneficiary_account_number;
    public string $beneficiary_name;
    public string $deposit_reference_number;
    public float $usdt_amount;
    public string $proof_of_deposit;
    public string $status = 'pending';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    /**
     * Map database row object to USDTRequest instance.
     */
    public static function map($row): ?self
    {
        if (!$row) return null;

        $request = new self();
        $request->id = (int)$row->id;
        $request->user_id = (int)$row->user_id;
        $request->receiving_bank_name = $row->receiving_bank_name;
        $request->receiving_bank_address = $row->receiving_bank_address;
        $request->routing_no_aba = $row->routing_no_aba;
        $request->beneficiary_account_number = $row->beneficiary_account_number;
        $request->beneficiary_name = $row->beneficiary_name;
        $request->deposit_reference_number = $row->deposit_reference_number;
        $request->usdt_amount = (float)$row->usdt_amount;
        $request->proof_of_deposit = $row->proof_of_deposit;
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
        $stmt = $db->prepare("SELECT * FROM usdt_purchase_requests WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::map($row) : null;
    }

    /**
     * Retrieve all purchase requests submitted by a specific user.
     *
     * @return self[]
     */
    public static function findByUserId(int $userId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM usdt_purchase_requests WHERE user_id = :user_id ORDER BY created_at DESC");
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
        $stmt = $db->query("SELECT * FROM usdt_purchase_requests WHERE status = 'pending' ORDER BY created_at DESC");
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
        $stmt = $db->query("SELECT * FROM usdt_purchase_requests WHERE status != 'pending' ORDER BY updated_at DESC");
        $rows = $stmt->fetchAll();

        $requests = [];
        foreach ($rows as $row) {
            $requests[] = self::map($row);
        }
        return $requests;
    }

    /**
     * Save the USDT request record (Insert if new, Update if exists).
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
                INSERT INTO usdt_purchase_requests (
                    user_id, receiving_bank_name, receiving_bank_address, routing_no_aba,
                    beneficiary_account_number, beneficiary_name, deposit_reference_number,
                    usdt_amount, proof_of_deposit, status, created_at, updated_at
                ) VALUES (
                    :user_id, :receiving_bank_name, :receiving_bank_address, :routing_no_aba,
                    :beneficiary_account_number, :beneficiary_name, :deposit_reference_number,
                    :usdt_amount, :proof_of_deposit, :status, :created_at, :updated_at
                )
            ");

            $result = $stmt->execute([
                ':user_id' => $this->user_id,
                ':receiving_bank_name' => $this->receiving_bank_name,
                ':receiving_bank_address' => $this->receiving_bank_address,
                ':routing_no_aba' => $this->routing_no_aba,
                ':beneficiary_account_number' => $this->beneficiary_account_number,
                ':beneficiary_name' => $this->beneficiary_name,
                ':deposit_reference_number' => $this->deposit_reference_number,
                ':usdt_amount' => $this->usdt_amount,
                ':proof_of_deposit' => $this->proof_of_deposit,
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
                UPDATE usdt_purchase_requests SET
                    user_id = :user_id,
                    receiving_bank_name = :receiving_bank_name,
                    receiving_bank_address = :receiving_bank_address,
                    routing_no_aba = :routing_no_aba,
                    beneficiary_account_number = :beneficiary_account_number,
                    beneficiary_name = :beneficiary_name,
                    deposit_reference_number = :deposit_reference_number,
                    usdt_amount = :usdt_amount,
                    proof_of_deposit = :proof_of_deposit,
                    status = :status,
                    updated_at = :updated_at
                WHERE id = :id
            ");

            return $stmt->execute([
                ':id' => $this->id,
                ':user_id' => $this->user_id,
                ':receiving_bank_name' => $this->receiving_bank_name,
                ':receiving_bank_address' => $this->receiving_bank_address,
                ':routing_no_aba' => $this->routing_no_aba,
                ':beneficiary_account_number' => $this->beneficiary_account_number,
                ':beneficiary_name' => $this->beneficiary_name,
                ':deposit_reference_number' => $this->deposit_reference_number,
                ':usdt_amount' => $this->usdt_amount,
                ':proof_of_deposit' => $this->proof_of_deposit,
                ':status' => $this->status,
                ':updated_at' => $this->updated_at
            ]);
        }
    }
}
