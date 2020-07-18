<?php

namespace Fruitware\WhmcsWrapper;

use Fruitware\WhmcsWrapper\Config\DefaultConfig;
use Fruitware\WhmcsWrapper\Connect\Connector;
use Fruitware\WhmcsWrapper\Exception\RuntimeException;
use GuzzleHttp\Exception\ClientException;

/**
 * @package Fruitware\WhmcsWrapper
 * @author   Fruits Foundation <foundation@fruits.agency>
 *
 * @link https://developers.whmcs.com/api/
 *
 * Class Facade
 * @property Connector|null connector
 */
class Facade
{
    protected ?Connector $connector;

    /**
     * @return Connector
     *
     * @throws RuntimeException
     */
    protected function connector(): Connector
    {
        if (!$this->connector) {
            throw new RuntimeException('You should call `connect` method first and pass the connection data');
        }
        return $this->connector;
    }

    /**
     * Damn simple connect method:
     *
     *     $whmscClient = Facade::run()->connect(<whmcs home url>, <identifier>, <secret>);
     *     $whmscClient->call('GetHealthStatus');
     *
     * OR since method is chained to Facade is can be a one-liner:,
     *
     *     Facade::run()->connect(<whmcs home url>, <identifier>, <secret>)->call('GetHealthStatus');
     *
     * “Authenticating With API Credentials” method is the only available right now
     * @link https://developers.whmcs.com/api/authentication/
     *
     * @param  string  $uri  path to your WHMCS installation (HTTP_ROOT)
     * @param  string  $key  WHMCS Identifier
     * @param  string  $secret  WHMCS Identifier's Secret key
     * @param  array|string[]  $params  `form_fields` that will be used for each API-call e.g. 'responsetype' => 'json'
     * @param  array  $config  Request options to apply. See \GuzzleHttp\RequestOptions
     * @return Facade
     *
     * @throws RuntimeException
     */
    public function connect(
        string $uri,
        string $key,
        string $secret,
        array $params = [],
        array $config = []
    ): Facade {
        $this->connector = self::getConnector($uri, $key, $secret, $params, $config);

        return $this;
    }


    /**
     * Direct access to the connector object
     *
     * “Authenticating With API Credentials” method is the only available right now
     * @link https://developers.whmcs.com/api/authentication/
     *
     * @param  string  $uri  path to your WHMCS installation (HTTP_ROOT)
     * @param  string  $key  WHMCS Identifier
     * @param  string  $secret  WHMCS Identifier's Secret key
     * @param  array|string[]  $params  `form_fields` that will be used for each API-call e.g. 'responsetype' => 'json'
     * @param  array  $config  Request options to apply. See \GuzzleHttp\RequestOptions
     * @return Connector
     *
     * @throws RuntimeException
     */
    public static function getConnector(
        string $uri,
        string $key,
        string $secret,
        array $params = [],
        array $config = []
    ): ?Connector {

        return Connector::connect(DefaultConfig::i(
            $uri,
            $key,
            $secret,
            $params,
            $config
        ));
    }


    /**
     * Method used to access instantiated object with single connection
     * @return Facade
     */
    final public static function run(): Facade
    {
        static $self;
        if (!$self) {
            $self = new self();
        }
        return $self;
    }

    /**
     * Suitable for any API-call
     *
     * @link https://developers.whmcs.com/api/api-index/
     *
     * @param  string  $action
     * @param  array  $params
     * @param  bool  $skipValidation
     *
     * @return array|null
     *
     * @throws RuntimeException|ClientException
     */
    final public function call(string $action, array $params = [], bool $skipValidation = false): ?array
    {
        return self::run()->connector()->call($action, $params, $skipValidation);
    }
}
