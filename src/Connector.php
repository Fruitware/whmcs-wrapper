<?php

namespace Fruitware\WhmcsWrapper;

use Fruitware\WhmcsWrapper\Exception\RuntimeException;
use Fruitware\WhmcsWrapper\Lib\pattern\Singleton;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

/**
 * Class Connector
 *
 * @package Fruitware\WhmcsWrapper
 */
class Connector extends Singleton
{
    /**
     * WHMCS API wrapper
     *
     * @example https://github.com/PeteBishwhip/WHMCSAPI
     */
    protected Client $client;

    protected string $whmcsUri;
    protected string $whmcsIdentifier;
    protected string $whmcsSecret;
    protected string $whmcsEnabled;

    protected array $config = [
        'timeout' => 3.0,
    ];

    protected array $defaultAttributes;

    /**
     * @param  string  $whmcsUri
     * @param  string  $whmcsIdentifier
     * @param  string  $whmcsSecret
     * @param  array  $config
     */
    public function setConfig(string $whmcsUri, string $whmcsIdentifier, string $whmcsSecret, array $config = []): void
    {
        $this->whmcsUri = $whmcsUri;
        $this->whmcsIdentifier = $whmcsIdentifier;
        $this->whmcsSecret = $whmcsSecret;
        $this->whmcsEnabled = true;
        if (!empty($config)) {
            $this->config = $config;
        }
    }

    /**
     * @param  string  $command
     * @param  array []  $attributes
     *
     * @param  bool  $skipValidation
     *
     * @return array
     * @throws RuntimeException
     */
    public function executeCommand(string $command, array $attributes = [], bool $skipValidation = false): ?array
    {
        try {
            if ($skipValidation) {
                $attributes['skipvalidation'] = true;
            }
            $attributes['action'] = $command;
            $requestData = array_merge($this->defaultAttributes, $attributes);
            $response = $this->client->request('post', '/includes/api.php', [
                'form_params' => $requestData
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('Code not 200: '.$response->getReasonPhrase(), $response->getStatusCode());
            }

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new RuntimeException('GuzzleException:'.$e->getMessage(), $e->getCode());

        } catch (JsonException $e) {
            throw new RuntimeException('JsonException:'.$e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return Client
     * @throws RuntimeException
     */
    protected function getClient(): Client
    {
        if (!$this->client) {
            if (!$this->whmcsEnabled) {
                throw new RuntimeException('To use the Connector set configuration: <uri>, <identifier>, <secret>');
            }
            $this->client = new Client(
                array_merge([
                    'base_uri' => $this->whmcsUri
                ],
                    $this->config
                )
            );
        }
        return $this->client;
    }
}
