<?php

namespace Aphonix\Pay\Gateways\Wechat;

use Symfony\Component\HttpFoundation\Request;
use Aphonix\Supports\Collection;

class ScanGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @param string $endpoint
     * @param array $payload
     * @return Collection
     * @throws \Aphonix\Pay\Exceptions\GatewayException
     * @throws \Aphonix\Pay\Exceptions\InvalidArgumentException
     * @throws \Aphonix\Pay\Exceptions\InvalidSignException
     */
    public function pay($endpoint, array $payload): Collection
    {
        $payload['spbill_create_ip'] = Request::createFromGlobals()->server->get('SERVER_ADDR');
        $payload['trade_type'] = $this->getTradeType();

        return $this->preOrder('pay/unifiedorder', $payload);
    }

    /**
     * Get trade type config.
     *
     * @return string
     */
    protected function getTradeType(): string
    {
        return 'NATIVE';
    }
}
