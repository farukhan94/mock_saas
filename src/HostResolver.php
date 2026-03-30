<?php

namespace App;

class HostResolver
{
    private string $baseDomain;

    public function __construct(string $baseDomain)
    {
        $this->baseDomain = strtolower($baseDomain);
    }

    /**
     * Normalizes the hostname by removing port and converting to lowercase.
     */
    public function getNormalizedHost(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        // Remove port if present
        if (($pos = strpos($host, ':')) !== false) {
            $host = substr($host, 0, $pos);
        }
        return strtolower($host);
    }

    /**
     * Checks if the current request is for the Admin UI.
     */
    public function isAdminHost(string $currentHost): bool
    {
        return $currentHost === $this->baseDomain;
    }

    /**
     * Validates domain format.
     */
    public static function isValidDomain(string $domain): bool
    {
        // Simple domain validation regex
        return preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/i', $domain);
    }
}
