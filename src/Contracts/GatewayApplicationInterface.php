<?php

namespace Aphonix\Pay\Contracts;

use Aphonix\Supports\Collection;
use Symfony\Component\HttpFoundation\Response;

interface GatewayApplicationInterface
{
    /**
     * To pay.
     *
     * @param $gateway
     * @param $params
     * @return Collection|Response
     */
    public function pay($gateway, $params);

    /**
     * Query an order.
     *
     * @param string|array $order
     * @param bool         $refund
     *
     * @return Collection
     */

    public function find($order, $refund);

    /**
     * Refund an order.
     *
     * @param array $order
     *
     * @return Collection
     */
    public function refund($order);

    /**
     * Cancel an order.
     *
     * @param string|array $order
     *
     * @return Collection
     */
    public function cancel($order);

    /**
     * Close an order.
     *
     * @param string|array $order
     *
     * @return Collection
     */
    public function close($order);

    /**
     * Verify a request.
     *
     * @param string|null $content
     * @param bool        $refund
     *
     * @return Collection
     */
    public function verify($content, $refund);

    /**
     * Echo success to server.
     *
     * @return Response
     */
    public function success();
}
