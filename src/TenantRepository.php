<?php

namespace App;

class TenantRepository
{
    private $db;

    /**
     * @param PDO|CloudflareD1Handler $db
     */
    public function __construct($db)
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
        // Cloudflare D1 uses ? for parameters, PDO uses :name
        // So we'll use a portable ? approach or handle it in the handler
        $stmt = $this->db->prepare("SELECT * FROM tenants WHERE domain = ? LIMIT 1");
        $stmt->execute([strtolower($domain)]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $name, string $domain): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO tenants (tenant_name, domain, created_at, updated_at) 
            VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        return $stmt->execute([
            $name,
            strtolower($domain)
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tenants WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
