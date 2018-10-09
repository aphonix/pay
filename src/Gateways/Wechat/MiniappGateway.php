<?php

namespace Aphonix\Pay\Gateways\Wechat;

use Aphonix\Pay\Gateways\Wechat;
use Aphonix\Supports\Collection;

class MiniappGateway extends MpGateway
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
        $payload['appid'] = $this->config->get('miniapp_id');

        $this->mode !== Wechat::MODE_SERVICE ?: $payload['sub_appid'] = $this->config->get('sub_miniapp_id');

        return parent::pay($endpoint, $payload);
    }
}
