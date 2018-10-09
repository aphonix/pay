<?php

namespace Aphonix\Pay\Gateways\Wechat;

use Aphonix\Pay\Contracts\GatewayInterface;
use Aphonix\Pay\Gateways\Wechat;
use Aphonix\Pay\Log;
use Aphonix\Supports\Collection;
use Aphonix\Supports\Config;

abstract class Gateway implements GatewayInterface
{
    /**
     * Config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Mode.
     *
     * @var string
     */
    protected $mode;

    /**
     * Bootstrap.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->mode = $this->config->get('mode', Wechat::MODE_NORMAL);
    }

    /**
     * Pay an order.
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return Collection
     */
    abstract public function pay($endpoint, array $payload);

    /**
     * Get trade type config.
     *
     * @return string
     */
    abstract protected function getTradeType();

    /**
     * Preorder an order.
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return Collection
     */
    protected function preOrder($endpoint, $payload): Collection
    {
        $payload['sign'] = Support::generateSign($payload, $this->config->get('key'));

        Log::debug('Pre Order:', [$endpoint, $payload]);

        return Support::requestApi($endpoint, $payload, $this->config->get('key'));
    }
}
