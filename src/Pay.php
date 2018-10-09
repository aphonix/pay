<?php

namespace Aphonix\Pay;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Aphonix\Pay\Contracts\GatewayApplicationInterface;
use Aphonix\Pay\Exceptions\InvalidGatewayException;
use Aphonix\Supports\Config;
use Aphonix\Supports\Str;

/**
 * @method static \Aphonix\Pay\Gateways\Alipay alipay(array $config) 支付宝
 * @method static \Aphonix\Pay\Gateways\Wechat wechat(array $config) 微信
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
     *
     * @param string $method
     *
     * @return GatewayApplicationInterface
     */
    protected function create($method)
    {
        !$this->config->has('log.file') ?: $this->registeLog();

        $gateway = __NAMESPACE__.'\\Gateways\\'.Str::studly($method);

        if (class_exists($gateway)) {
            return self::make($gateway);
        }

        throw new InvalidGatewayException("Gateway [{$method}] Not Exists");
    }

    /**
     * Make a gateway.
     *
     * @param string $gateway
     *
     * @return GatewayApplicationInterface
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
     * Registe log service.
     *
     * @author yansongda <me@yansongda.cn>
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
     * @param string $method
     * @param array  $params
     *
     * @return GatewayApplicationInterface
     */
    public static function __callStatic($method, $params)
    {
        $app = new self(...$params);

        return $app->create($method);
    }
}
