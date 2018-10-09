<?php

namespace Aphonix\Pay\Gateways\Alipay;

use Aphonix\Pay\Contracts\GatewayInterface;
use Aphonix\Pay\Log;
use Aphonix\Supports\Collection;
use Aphonix\Supports\Config;

class ScanGateway implements GatewayInterface
{
    /**
     * Config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Bootstrap.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Pay an order.
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return Collection
     * @throws \Aphonix\Pay\Exceptions\GatewayException
     * @throws \Aphonix\Pay\Exceptions\InvalidConfigException
     * @throws \Aphonix\Pay\Exceptions\InvalidSignException
     */
    public function pay($endpoint, array $payload): Collection
    {
        $payload['method'] = $this->getMethod();
        $payload['biz_content'] = json_encode(array_merge(
            json_decode($payload['biz_content'], true),
            ['product_code' => $this->getProductCode()]
        ));
        $payload['sign'] = Support::generateSign($payload, $this->config->get('private_key'));

        Log::debug('Paying A Scan Order:', [$endpoint, $payload]);

        return Support::requestApi($payload, $this->config->get('ali_public_key'));
    }

    /**
     * Get method config.
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return 'alipay.trade.precreate';
    }

    /**
     * Get productCode config.
     *
     * @return string
     */
    protected function getProductCode(): string
    {
        return '';
    }
}
