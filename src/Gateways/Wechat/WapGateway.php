<?php

namespace Aphonix\Pay\Gateways\Wechat;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class WapGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @param string $endpoint
     * @param array $payload
     * @return Response
     * @throws \Aphonix\Pay\Exceptions\GatewayException
     * @throws \Aphonix\Pay\Exceptions\InvalidArgumentException
     * @throws \Aphonix\Pay\Exceptions\InvalidSignException
     */
    public function pay($endpoint, array $payload): Response
    {
        $payload['trade_type'] = $this->getTradeType();

        $data = $this->preOrder('pay/unifiedorder', $payload);

        $url = is_null($this->config->get('return_url')) ? $data->mweb_url : $data->mweb_url.
                        '&redirect_url='.urlencode($this->config->get('return_url'));

        return RedirectResponse::create($url);
    }

    /**
     * Get trade type config.
     *
     * @return string
     */
    protected function getTradeType(): string
    {
        return 'MWEB';
    }
}
