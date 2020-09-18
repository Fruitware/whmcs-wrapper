<?php

namespace Fruitware\WhmcsWrapper\Config;

use function filter_var;
use function rtrim;

/**
 * Class DefaultConfig
 *
 * @package Fruitware\WhmcsWrapper
 * @author   Fruits Foundation <foundation@fruits.agency>
 */
final class DefaultConfig implements ConfigInterface
{
    protected $apiRequestUrl = '/includes/api.php';
    protected $key;
    protected $secret;

    protected $postDefaultParams = [
        'responsetype' => 'json'
    ];

    protected $clientOptions = [
        'base_uri' => '',
    ];

    /**
     * @inheritDoc
     */
    public function __construct(
        string $apiBaseUri,
        string $key,
        string $secret,
        array $postDefaultParams,
        array $clientOptions
    ) {
        $this->key = $key;
        $this->secret = $secret;

        if ($postDefaultParams) {
            $this->postDefaultParams = $postDefaultParams;
        }
        if ($clientOptions) {
            $this->clientOptions = $clientOptions;
        }
        $this->clientOptions['base_uri'] = filter_var(rtrim($apiBaseUri, "/"), FILTER_SANITIZE_URL);
    }

    /**
     * @inheritDoc
     */
    public static function i(
        string $baseUri,
        string $identifier,
        string $secret,
        array $defaultConfig = [],
        array $clientOptions = []
    ): ConfigInterface {
        return new self(
            $baseUri,
            $identifier,
            $secret,
            $defaultConfig,
            $clientOptions
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
     * @inheritDoc
     */
    public function prepareConfig(): array
    {
        return $this->clientOptions;
    }

    /**
     * @inheritDoc
     */
    public function prepareRequestOptions(string $action, array $params, bool $skipValidation): array
    {
        if ($skipValidation) {
            $params['skipvalidation'] = true;
        }
        $params['action'] = $action;

        $result = array_merge(
            $this->postDefaultParams, [
            'identifier' => $this->key,
            'secret' => $this->secret,
        ], $params);

        if (!empty($result['customfields'])) {
            $result['customfields'] = base64_encode(serialize($result['customfields']));
        }
        return [
            'form_params' => $result
        ];
    }
}
