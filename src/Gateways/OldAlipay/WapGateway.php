<?php

namespace Aphonix\Pay\Gateways\OldAlipay;

use Symfony\Component\HttpFoundation\Response;
use Aphonix\Pay\Contracts\GatewayInterface;
use Aphonix\Pay\Log;
use Aphonix\Supports\Config;

class WapGateway implements GatewayInterface
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

        Log::debug('Paying A Web/Wap Order:', [$endpoint, $payload]);

        return $this->buildPayHtml($endpoint, $payload);
    }

    /**
     * Build Html response.
     *
     * @param string $endpoint
     * @param array $payload
     *
     * @return Response
     */
    protected function buildPayHtml($endpoint, $payload): Response
    {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . $endpoint . "' method='GET'>";
        foreach ($payload as $key => $val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";

        return Response::create($sHtml);
    }

    /**
     * Get method config.
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return 'alipay.wap.create.direct.pay.by.user';
    }
}
