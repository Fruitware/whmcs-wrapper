<?php

namespace Fruitware\WhmcsWrapper\Config;

use function rtrim;

/**
 * Class DefaultConfig
 *
 * @package Fruitware\WhmcsWrapper\Config
 */
final class DefaultConfig implements ConfigInterface
{
    /**
     * @var string
     */
    protected string $apiRequestUrl = '/includes/api.php';

    /**
     * @var string
     */
    protected string $apiBaseUri;

    /**
     * @var string
     */
    protected string $apiIdentifier;

    /**
     * @var string
     */
    protected string $apiSecret;

    /**
     * @var array
     */
    protected array $postDefaultParams;

    /**
     * @var array
     */
    protected array $requestOptions;

    /**
     * @inheritDoc
     */
    protected function __construct(
        string $apiBaseUri,
        string $apiIdentifier,
        string $apiSecret,
        array $postDefaultParams,
        array $requestOptions
    ) {
        /**
         * Filtering trailing slash
         */
        $this->apiBaseUri = rtrim($apiBaseUri, '/');

        $this->apiIdentifier = $apiIdentifier;
        $this->apiSecret = $apiSecret;
        $this->postDefaultParams = $postDefaultParams;
        $this->requestOptions = $requestOptions;
    }

    /**
     * @inheritDoc
     */
    public static function i(
        string $baseUri,
        string $identifier,
        string $secret,
        array $defaultConfig = ['responsetype' => 'json'],
        array $requestOptions = ['timeout' => 3.0]
    ): ConfigInterface {
        return new self(
            $baseUri,
            $identifier,
            $secret,
            $defaultConfig,
            $requestOptions
        );
    }


    /**
     * Transmitting securely API POST request url
     *
     * @return string
     */
    public function getRequestUrl(): string
    {
        return $this->apiRequestUrl;
    }

    /**
     * Transmitting given authorization `identifier` to configure http-client
     *
     * @return string
     */
    public function getAuthIdentifier(): string
    {
        return $this->apiIdentifier;
    }


    /**
     * Transmitting `base_uri` value to configure http-client
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->apiBaseUri;
    }

    /**
     * @return string `secret` to configure http-client
     */
    public function getAuthSecret(): string
    {
        return $this->apiSecret;
    }

    /**
     * @return array default http-client options
     */
    public function getRequestOptions(): array
    {
        return $this->requestOptions;
    }

    /**
     * @param  array  $options
     * @return ConfigInterface
     */
    public function updateRequestOptions(array $options = []): ConfigInterface
    {
        if ($options) {
            $this->requestOptions = array_merge($this->requestOptions, $options);
        }
        return $this;
    }


    /**
     * @param  array  $params
     * @return ConfigInterface
     */
    public function updateDefaultParams(array $params = []): ConfigInterface
    {
        if ($params) {
            $this->postDefaultParams = array_merge($this->postDefaultParams, $params);
        }
        return $this;
    }

    /**
     * Transmitting securely default form_field POST-params to the Connector
     *
     * @return array
     */
    public function getDefaultParams(): array
    {
        return $this->postDefaultParams + [
                'identifier' => $this->apiIdentifier,
                'secret' => $this->apiSecret,
            ];
    }
}