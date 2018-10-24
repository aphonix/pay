<?php

namespace Aphonix\Pay;

use Aphonix\Pay\Gateways\OldAlipay;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Aphonix\Pay\Contracts\GatewayApplicationInterface;
use Aphonix\Pay\Exceptions\InvalidGatewayException;
use Aphonix\Supports\Config;
use Aphonix\Supports\Str;
use Aphonix\Pay\Gateways\Alipay;
use Aphonix\Pay\Gateways\Wechat;

/**
 * @method static Alipay alipay(array $config) 支付宝
 * @method static Wechat wechat(array $config) 微信
 * @method static OldAlipay old_alipay(array $config) 老版本支付宝支付
 */
class Pay
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
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    /**
     * Create a instance.
     * @param $method
     * @return GatewayApplicationInterface
     * @throws InvalidGatewayException
     */
    protected function create($method)
    {
        !$this->config->has('log.file') ?: $this->registeLog();

        $gateway = __NAMESPACE__ . '\\Gateways\\' . Str::studly($method);

        if (class_exists($gateway)) {
            return self::make($gateway);
        }

        throw new InvalidGatewayException("Gateway [{$method}] Not Exists");
    }

    /**
     * Make a gateway.
     * @param $gateway
     * @return mixed
     * @throws InvalidGatewayException
     */
    protected function make($gateway)
    {
        $app = new $gateway($this->config);

        if ($app instanceof GatewayApplicationInterface) {
            return $app;
        }

        throw new InvalidGatewayException("Gateway [$gateway] Must Be An Instance Of GatewayApplicationInterface");
    }

    /**
     * Register log service.
     * @throws \Exception
     */
    protected function registeLog()
    {
        $handler = new StreamHandler(
            $this->config->get('log.file'),
            $this->config->get('log.level', Logger::WARNING)
        );
        $handler->setFormatter(new LineFormatter("%datetime% > %level_name% > %message% %context% %extra%\n\n"));

        $logger = new Logger('yansongda.pay');
        $logger->pushHandler($handler);

        Log::setLogger($logger);
    }

    /**
     * Magic static call.
     *
     * @param $method
     * @param $params
     * @return GatewayApplicationInterface
     * @throws InvalidGatewayException
     */
    public static function __callStatic($method, $params)
    {
        $app = new self(...$params);

        return $app->create($method);
    }
}
