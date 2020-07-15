<?php

namespace Fruitware\WhmcsWrapper;

use Fruitware\WhmcsWrapper\Lib\pattern\Singleton;

/**
 * Class Facade
 * @package Fruitware\WhmcsWrapper
 */
class Facade extends Singleton
{
    protected Connector $connector;

    /**
     * Chain-ready function
     *
     * @param  string  $whmcsUri
     * @param  string  $whmcsIdentifier
     * @param  string  $whmcsSecret
     * @param  array  $config
     * @return Facade
     */
    public function setConfig(string $whmcsUri, string $whmcsIdentifier, string $whmcsSecret, array $config = []): self
    {
        $this->connector = Connector::init();
        $this->connector->setConfig($whmcsUri, $whmcsIdentifier, $whmcsSecret, $config);
        return $this;
    }

    /**
     * @param  string  $command
     * @param  array  $attributes
     * @param  bool  $skipValidation
     * @return array|null
     * @throws Exception\RuntimeException
     */
    public function executeCommand(string $command, array $attributes = [], bool $skipValidation = false): ?array
    {
        return $this->connector->executeCommand($command, $attributes, $skipValidation);
    }
}
