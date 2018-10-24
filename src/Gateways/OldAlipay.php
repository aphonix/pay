<?php
/**
 * Created by PhpStorm.
 * User: HouJie
 * Date: 2018/10/24
 * Time: 11:57
 * Desc:
 */

namespace Aphonix\Pay\Gateways;

use Aphonix\Pay\Exceptions\GatewayException;
use Aphonix\Pay\Gateways\OldAlipay\AppGateway;
use Aphonix\Pay\Gateways\OldAlipay\Support;
use Aphonix\Pay\Gateways\OldAlipay\WapGateway;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Aphonix\Pay\Contracts\GatewayApplicationInterface;
use Aphonix\Pay\Contracts\GatewayInterface;
use Aphonix\Pay\Exceptions\InvalidGatewayException;
use Aphonix\Pay\Exceptions\InvalidSignException;
use Aphonix\Pay\Log;
use Aphonix\Supports\Collection;
use Aphonix\Supports\Config;
use Aphonix\Supports\Str;


/**
 * @method WapGateway wap(array $config) 手机网站支付
 * @method AppGateway app(array $config) APP支付
 */
class OldAlipay implements GatewayApplicationInterface
{
    /**
     * Config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Alipay payload.
     *
     * @var array
     */
    protected $payload;

    /**
     * Alipay gateway.
     *
     * @var string
     */
    protected $gateway;

    /**
     * Bootstrap.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->gateway = Support::baseUri($this->config->get('mode', 'normal'));
        $this->payload = [
            'partner' => $this->config->get('partner'),
            'seller_id' => $this->config->get('partner'),
            'service' => '',
            'payment_type' => 1,
            '_input_charset' => 'utf-8',
            'sign_type' => 'RSA',
            'return_url' => $this->config->get('return_url'),
            'notify_url' => $this->config->get('notify_url'),
            'sign' => '',
        ];
    }

    /**
     * Pay an order.
     *
     * @param string $gateway
     * @param array $params
     * @return Collection|Response
     * @throws InvalidGatewayException
     */
    public function pay($gateway, $params = [])
    {
        $this->payload = array_merge($this->payload, $params);

        $gateway = get_class($this) . '\\' . Str::studly($gateway) . 'Gateway';

        if (class_exists($gateway)) {
            return $this->makePay($gateway);
        }

        throw new InvalidGatewayException("Pay Gateway [{$gateway}] not exists");
    }

    /**
     * Verify sign.
     *
     * @param null $content
     * @param bool $refund
     * @return Collection
     * @throws InvalidSignException
     * @throws \Aphonix\Pay\Exceptions\InvalidConfigException
     */
    public function verify($content = null, $refund = false): Collection
    {
        $request = Request::createFromGlobals();

        $data = $request->request->count() > 0 ? $request->request->all() : $request->query->all();

        Log::debug('Receive Alipay Request:', $data);

        if (Support::verifySign($data, $this->config->get('ali_public_key'))) {
            return new Collection($data);
        }

        Log::warning('Alipay Sign Verify FAILED', $data);

        throw new InvalidSignException('Alipay Sign Verify FAILED', $data);
    }

    /**
     * Query an order.
     *
     * @param array|string $order
     * @param bool $refund
     * @return Collection
     * @throws InvalidSignException
     * @throws \Aphonix\Pay\Exceptions\GatewayException
     * @throws \Aphonix\Pay\Exceptions\InvalidConfigException
     */
    public function find($order, $refund = false): Collection
    {
        $this->payload['service'] = $refund ? 'alipay.trade.fastpay.refund.query' : 'single_trade_query';
        $this->payload = array_merge($this->payload, $order);

        unset($this->payload['notify_url'], $this->payload['return_url'], $this->payload['seller_id'], $this->payload['payment_type']);

        $this->payload['sign'] = Support::generateSign($this->payload, $this->config->get('private_key'));

        Log::debug('Alipay Find An Order:', [$this->gateway, $this->payload]);

        return Support::requestApi($this->payload, $this->config->get('ali_public_key'));
    }

    /**
     * Refund an order.
     *
     * @param array $order
     * @return Collection
     * @throws InvalidSignException
     * @throws \Aphonix\Pay\Exceptions\GatewayException
     * @throws \Aphonix\Pay\Exceptions\InvalidConfigException
     */
    public function refund($order): Collection
    {
        $this->payload['service'] = 'refund_fastpay_by_platform_nopwd';
        $this->payload = array_merge($this->payload, $order);
        $this->payload['sign'] = Support::generateSign($this->payload, $this->config->get('private_key'));

        Log::debug('Alipay Refund An Order:', [$this->gateway, $this->payload]);

        return Support::requestApi($this->payload, $this->config->get('ali_public_key'));
    }

    /**
     * Cancel an order.
     *
     * @param array|string $order
     * @return Collection
     * @throws GatewayException
     */
    public function cancel($order): Collection
    {
        throw new GatewayException('Old Alipay is not support！');
    }

    /**
     * Close an order.
     *
     * @param array|string $order
     * @return Collection
     * @throws GatewayException
     */
    public function close($order): Collection
    {
        throw new GatewayException('Old Alipay is not support！');
    }

    /**
     * Download bill.
     *
     * @param string|array $bill
     * @return string
     * @throws GatewayException
     */
    public function download($bill): string
    {
        throw new GatewayException('Old Alipay is not support！');
    }

    /**
     * Reply success to alipay.
     *
     * @return Response
     */
    public function success(): Response
    {
        return Response::create('success');
    }

    /**
     * Make pay gateway.
     *
     * @param $gateway
     * @return Collection|Response
     * @throws InvalidGatewayException
     */
    protected function makePay($gateway)
    {
        $app = new $gateway($this->config);

        if ($app instanceof GatewayInterface) {
            return $app->pay($this->gateway, $this->payload);
        }

        throw new InvalidGatewayException("Pay Gateway [{$gateway}] Must Be An Instance Of GatewayInterface");
    }

    /**
     * Magic pay.
     *
     * @param $method
     * @param $params
     * @return Collection|Response
     * @throws InvalidGatewayException
     */
    public function __call($method, $params)
    {
        return $this->pay($method, ...$params);
    }
}