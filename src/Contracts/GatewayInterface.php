<?php

namespace Aphonix\Pay\Contracts;

use Aphonix\Supports\Collection;
use Symfony\Component\HttpFoundation\Response;

interface GatewayInterface
{
    /**
     * Pay an order.
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return Collection|Response
     */
    public function pay($endpoint, array $payload);
}
