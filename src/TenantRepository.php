<?php

namespace App;

use PDO;

class TenantRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM tenants ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function findByDomain(string $domain): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM tenants WHERE domain = :domain LIMIT 1");
        $stmt->execute(['domain' => strtolower($domain)]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $name, string $domain): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO tenants (tenant_name, domain, created_at, updated_at) 
            VALUES (:name, :domain, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        return $stmt->execute([
            'name' => $name,
            'domain' => strtolower($domain)
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tenants WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
