<?php

namespace Fruitware\WhmcsWrapper\Connect;

use Exception;
use Fruitware\WhmcsWrapper\{Config\ConfigInterface, Exception\RuntimeException};
use GuzzleHttp\{Client, ClientInterface, Promise\AggregateException};
use function json_decode;

/**
 * Class Connector
 *
 * @package Fruitware\WhmcsWrapper
 * @author   Fruits Foundation <foundation@fruits.agency>
 */
final class Connector
{
    /**
     * WHMCS API wrapper
     * @var ClientInterface|null
     */
    protected ?ClientInterface $client = null;

    /**
     * @var ConfigInterface|null
     */
    protected ?ConfigInterface $config;

    protected function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    protected function getClient(): ClientInterface
    {
        if (!$this->client) {
            $this->client = new Client($this->config->prepareConfig());
        }

        return $this->client;
    }

    /**
     * Main method and only one available (excluding the constructor)
     *
     * @link https://developers.whmcs.com/api-reference/addclient/
     *
     * @param  string  $action
     * @param  array  $params
     * @param  bool  $skipValidation
     *
     * @return array
     * @throws RuntimeException
     */
    public function call(string $action, array $params = [], bool $skipValidation = false): ?array
    {
        try {
            $response = $this->getClient()->post(
                $this->config->getRequestUrl(),
                $this->config->prepareRequestOptions($action, $params, $skipValidation)
            );
            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException(
                    "Error: Status {$response->getStatusCode()}, reason: {$response->getReasonPhrase()}",
                    $response->getStatusCode()
                );
            }

            return json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR | JSON_THROW_ON_ERROR
            );
        } catch (AggregateException $e) {
            throw new RuntimeException('GuzzleException:'.$e->getMessage(), $e->getCode());

        } catch (Exception $e) {
            throw new RuntimeException('JsonException:'.$e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param  ConfigInterface  $config
     * @return Connector
     *
     * @throws RuntimeException
     */
    public static function connect(ConfigInterface $config): Connector
    {
        $self = new self($config);

        /**
         * Validate the connection
         */
        $self->call('GetClientGroups');

        return $self;
    }
}
