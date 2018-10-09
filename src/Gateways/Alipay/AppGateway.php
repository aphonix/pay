<?php

namespace Aphonix\Pay\Gateways\Alipay;

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
     * @param array  $payload
     *
     * @return Response
     */
    public function pay($endpoint, array $payload): Response
    {
        $payload['method'] = $this->getMethod();
        $payload['biz_content'] = json_encode(array_merge(
            json_decode($payload['biz_content'], true),
            ['product_code' => $this->getProductCode()]
        ));
        $payload['sign'] = Support::generateSign($payload, $this->config->get('private_key'));

        Log::debug('Paying An App Order:', [$endpoint, $payload]);

        return Response::create(http_build_query($payload));
    }

    /**
     * Get method config.
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return 'alipay.trade.app.pay';
    }

    /**
     * Get productCode method.
     *
     * @return string
     */
    protected function getProductCode(): string
    {
        return 'QUICK_MSECURITY_PAY';
    }
}
