CREATE TABLE IF NOT EXISTS tenants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_name TEXT NOT NULL,
    domain TEXT NOT NULL UNIQUE,
    cloudflare_hostname_id TEXT, -- For real CF API integration
    status TEXT DEFAULT 'active', -- pending, active, failed
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Initial sample data
INSERT OR IGNORE INTO tenants (tenant_name, domain) VALUES ('Admin Panel', 'admin.mock_saas');
