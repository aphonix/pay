<?php

namespace Aphonix\Pay\Gateways\OldAlipay;

use Symfony\Component\HttpFoundation\Response;
use Aphonix\Pay\Contracts\GatewayInterface;
use Aphonix\Pay\Log;
use Aphonix\Supports\Config;

class AppGateway implements GatewayInterface
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
     * @param array $payload
     * @return Response
     * @throws \Aphonix\Pay\Exceptions\InvalidConfigException
     */
    public function pay($endpoint, array $payload): Response
    {
        $payload['service'] = $this->getMethod();

        $payload['sign'] = Support::generateSign($payload, trim($this->config->get('private_key')));

        Log::debug('Paying A App Order:', [$endpoint, $payload]);

        return Response::create(http_build_query($payload));
    }


    /**
     * Get method config.
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return 'mobile.securitypay.pay';
    }
}
