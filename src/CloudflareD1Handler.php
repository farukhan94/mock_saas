<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * A serverless-friendly HTTP bridge for Cloudflare D1.
 * This class mimics PDO behavior.
 */
class CloudflareD1Handler
{
    private Client $client;
    private string $accountId;
    private string $databaseId;
    private string $apiToken;

    public function __construct(string $apiToken, string $accountId, string $databaseId)
    {
        $this->apiToken = $apiToken;
        $this->accountId = $accountId;
        $this->databaseId = $databaseId;

        $this->client = new Client([
            'base_uri' => "https://api.cloudflare.com/client/v4/accounts/$accountId/d1/database/$databaseId/query",
            'headers' => [
                'Authorization' => "Bearer $apiToken",
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15,
        ]);
    }

    /**
     * Mocks PDO::prepare()
     */
    public function prepare(string $sql)
    {
        return new CloudflareD1Statement($this->client, $sql);
    }

    /**
     * Mocks PDO::query()
     */
    public function query(string $sql)
    {
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt;
    }
}

/**
 * Mocks PDOStatement class.
 */
class CloudflareD1Statement
{
    private Client $client;
    private string $sql;
    private array $results = [];
    private int $position = 0;

    public function __construct(Client $client, string $sql)
    {
        $this->client = $client;
        $this->sql = $sql;
    }

    public function execute(array $params = []): bool
    {
        // Reformat params from :name to $n for D1 if needed
        // For simplicity, we assume the user uses standard ? or D1-compliant params
        try {
            $response = $this->client->post('', [
                'json' => [
                    'sql' => $this->sql,
                    'params' => array_values($params) // D1 expects array of values
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['success']) {
                throw new \Exception("D1 Execute Failed: " . json_encode($data['errors']));
            }

            $this->results = $data['result'][0]['results'] ?? [];
            return true;
        } catch (GuzzleException $e) {
            throw new \Exception("D1 API Connection Error: " . $e->getMessage());
        }
    }

    public function fetchAll(): array
    {
        return $this->results;
    }

    public function fetch()
    {
        return $this->results[$this->position++] ?? null;
    }
}
