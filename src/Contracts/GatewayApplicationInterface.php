<?php

namespace Aphonix\Pay\Contracts;

interface GatewayApplicationInterface
{
    /**
     * To pay.
     *
     * @param string $gateway
     * @param array  $params
     *
     * @return Aphonix\Supports\Collection|Symfony\Component\HttpFoundation\Response
     */
    public function pay($gateway, $params);

    /**
     * Query an order.
     *
     * @param string|array $order
     * @param bool         $refund
     *
     * @return Aphonix\Supports\Collection
     */
    public function find($order, $refund);

    /**
     * Refund an order.
     *
     * @param array $order
     *
     * @return Aphonix\Supports\Collection
     */
    public function refund($order);

    /**
     * Cancel an order.
     *
     * @param string|array $order
     *
     * @return Aphonix\Supports\Collection
     */
    public function cancel($order);

    /**
     * Close an order.
     *
     * @param string|array $order
     *
     * @return Aphonix\Supports\Collection
     */
    public function close($order);

    /**
     * Verify a request.
     *
     * @param string|null $content
     * @param bool        $refund
     *
     * @return Aphonix\Supports\Collection
     */
    public function verify($content, $refund);

    /**
     * Echo success to server.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function success();
}
