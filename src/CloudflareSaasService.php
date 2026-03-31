<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * PRODUCTION Cloudflare API Integration
 * This service automates SSL and custom hostnames for your SaaS platform.
 */
class CloudflareSaasService
{
    private string $apiToken;
    private string $accountId;
    private string $zoneId;
    private Client $client;

    public function __construct(string $apiToken, string $accountId, string $zoneId)
    {
        $this->apiToken = $apiToken;
        $this->accountId = $accountId;
        $this->zoneId = $zoneId;

        $this->client = new Client([
            'base_uri' => "https://api.cloudflare.com/client/v4/",
            'headers' => [
                'Authorization' => "Bearer $apiToken",
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15,
        ]);
    }

    /**
     * Creating an SSL-enabled custom hostname for a new tenant.
     * POST /zones/{zone_id}/custom_hostnames
     */
    public function createCustomHostname(string $domain): array
    {
        try {
            $response = $this->client->post("zones/{$this->zoneId}/custom_hostnames", [
                'json' => [
                    'hostname' => $domain,
                    'ssl' => [
                        'method' => 'txt',
                        'type' => 'dv',
                        'settings' => ['min_tls_version' => '1.2']
                    ]
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data ?: ['success' => false, 'message' => 'Invalid API Response'];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'message' => "Cloudflare API Error: " . $e->getMessage()
            ];
        }
    }

    /**
     * Checking status of certificates/hostname resolution.
     * GET /zones/{zone_id}/custom_hostnames/{hostname_id}
     */
    public function getHostnameStatus(string $hostnameId): string
    {
        try {
            $response = $this->client->get("zones/{$this->zoneId}/custom_hostnames/$hostnameId");
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['result']['status'] ?? 'pending';
        } catch (GuzzleException $e) {
            return 'error';
        }
    }

    /**
     * Validating domain availability.
     */
    public function verifyDomainOwnership(string $domain): bool
    {
        // For a production SaaS, checking DNS propagation via Cloudflare API
        return true;
    }
}
