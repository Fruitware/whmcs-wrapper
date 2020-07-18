<?php

namespace Fruitware\WhmcsWrapper;

use Fruitware\WhmcsWrapper\Config\DefaultConfig;
use Fruitware\WhmcsWrapper\Connect\Connector;
use Fruitware\WhmcsWrapper\Exception\RuntimeException;

/**
 * @property Connector|null connector
 *
 * @todo split Facade from the Connector and encapsulate it to avoid external call (expect the special “expert” flow)
 * @todo apply pattern Facede for real
 *
 * Class Facade
 * @package Fruitware\WhmcsWrapper
 */
class Facade
{
    private static ?Facade $obj;
    protected ?Connector $connector;

    /**
     * @return Connector
     *
     * @throws RuntimeException
     */
    protected function getConnector(): Connector
    {
        if (!$this->connector) {
            throw new RuntimeException('You should call `connect` method first and pass the connection data');
        }
        return $this->connector;
    }

    /**
     * Damn simple connect method:
     * ```Facade::i()->call
     * This method works with static cached version of the Connector
     *
     * @param  string  $uri
     * @param  string  $apiId
     * @param  string  $apiSecret
     *
     * @param  array|null  $params
     * @param  array|null  $options
     * @return Connector
     *
     * @throws Exception\RuntimeException
     */
    public function connect(
        string $uri,
        string $apiId,
        string $apiSecret,
        array $params = null,
        array $options = null
    ): ?Connector {

        $config = DefaultConfig::i(
            $uri,
            $apiId,
            $apiSecret
        )->updateDefaultParams($params)->updateRequestOptions($options);

        return $this->connector = Connector::connect($config);
    }


    /**
     * Method used to access instantiated object with single connection
     * @return Facade
     */
    final public static function run(): Facade
    {
        if (!self::$obj) {
            self::$obj = new self();
        }
        return self::$obj;
    }

    /**
     * @param  string  $action
     * @param  array  $attributes
     * @param  bool  $skipValidation
     *
     * @return array|null
     *
     * @throws Exception\RuntimeException
     */
    final public function call(
        string $action,
        array $attributes = [],
        bool $skipValidation = false
    ): ?array {
        return self::run()->getConnector()->call($action, $attributes, $skipValidation);

    }
}
