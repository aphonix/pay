<?php

namespace Aphonix\Pay\Gateways\Wechat;

use Aphonix\Supports\Collection;

class PosGateway extends Gateway
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
        unset($payload['trade_type'], $payload['notify_url']);

        return $this->preOrder('pay/micropay', $payload);
    }

    /**
     * Get trade type config.
     *
     * @return string
     */
    protected function getTradeType(): string
    {
        return 'MICROPAY';
    }
}
