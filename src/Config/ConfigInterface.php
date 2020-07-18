<?php

namespace Fruitware\WhmcsWrapper\Config;

/**
 * @todo End uo with the configuration architecture
 * @package Fruitware\WhmcsWrapper
 */
interface ConfigInterface
{
    /**
     * Constructor expected to be protected but omitted to comply
     *
     * ConfigInterface constructor.
     * @param  string  $apiBaseUri
     * @param  string  $apiIdentifier
     * @param  string  $apiSecret
     * @param  array  $postDefaultParams
     * @param  array  $requestOptions
     */
    public function __construct(
        string $apiBaseUri,
        string $apiIdentifier,
        string $apiSecret,
        array $postDefaultParams,
        array $requestOptions
    );

    /**
     * Singleton config initialization
     *
     * @link https://developers.whmcs.com/api/authentication/
     *
     * @param  string  $baseUri  path to your WHMCS installation (HTTP_ROOT)
     *
     * @param  string  $identifier  WHMCS Identifier
     * @param  string  $secret  WHMCS Identifier's Secret key
     *
     * Members are used as `form_fields` for each API-call as POST-request
     * @param  array|string[]  $defaultConfig  default: array['responsetype' => 'json']
     *
     * @param  array  $requestOptions  Request options to apply. See \GuzzleHttp\RequestOptions
     * @return ConfigInterface
     */
    public static function i(
        string $baseUri,
        string $identifier,
        string $secret,
        array $defaultConfig = ['responsetype' => 'json'],
        array $requestOptions = ['timeout' => 3.0]
    ):
    ConfigInterface;

    /**
     * Transmitting securely default form_field POST-params to the Connector
     *
     * @return array
     */
    public function getDefaultParams(): array;

    /**
     * Transmitting securely API POST request url
     *
     * @return string
     */
    public function getRequestUrl(): string;

    /**
     * Transmitting given authorization `identifier` to configure http-client
     *
     * @return string
     */
    public function getAuthIdentifier(): string;

    /**
     * Transmitting `base_uri` value to configure http-client
     *
     * @return string
     */
    public function getBaseUri(): string;

    /**
     * Transmitting given authorization `secret` to configure http-client
     *
     * @return string
     */
    public function getAuthSecret(): string;

    /**
     * Setting default http-client options
     *
     * @return array
     */
    public function getRequestOptions(): array;


    /**
     * @param  array  $params
     * @return ConfigInterface
     */
    public function updateDefaultParams(array $params = []): ConfigInterface;


    /**
     * @param  array  $options
     * @return ConfigInterface
     */
    public function updateRequestOptions(array $options = []): ConfigInterface;
}