<?php

namespace Aphonix\Pay\Contracts;

interface GatewayInterface
{
    /**
     * Pay an order.
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return Aphonix\Supports\Collection|Symfony\Component\HttpFoundation\Response
     */
    public function pay($endpoint, array $payload);
}
