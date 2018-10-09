<?php

namespace Aphonix\Pay\Gateways\Wechat;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Aphonix\Pay\Gateways\Wechat;
use Aphonix\Pay\Log;
use Aphonix\Supports\Str;

class AppGateway extends Gateway
{
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
        $payload['appid'] = $this->config->get('appid');
        $payload['trade_type'] = $this->getTradeType();

        $this->mode !== Wechat::MODE_SERVICE ?: $payload['sub_appid'] = $this->config->get('sub_appid');

        $payRequest = [
            'appid'     => $this->mode === Wechat::MODE_SERVICE ? $payload['sub_appid'] : $payload['appid'],
            'partnerid' => $this->mode === Wechat::MODE_SERVICE ? $payload['sub_mch_id'] : $payload['mch_id'],
            'prepayid'  => $this->preOrder('pay/unifiedorder', $payload)->prepay_id,
            'timestamp' => strval(time()),
            'noncestr'  => Str::random(),
            'package'   => 'Sign=WXPay',
        ];
        $payRequest['sign'] = Support::generateSign($payRequest, $this->config->get('key'));

        Log::debug('Paying An App Order:', [$endpoint, $payRequest]);

        return JsonResponse::create($payRequest);
    }

    /**
     * Get trade type config.
     *
     * @return string
     */
    protected function getTradeType(): string
    {
        return 'APP';
    }
}
