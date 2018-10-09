<?php

namespace Aphonix\Pay\Exceptions;

class InvalidConfigException extends Exception
{
    /**
     * Raw error info.
     *
     * @var array|string
     */
    public $raw;

    /**
     * Bootstrap.
     *
     * @param string       $message
     * @param array|string $raw
     * @param int|string   $code
     */
    public function __construct($message, $raw = '', $code = 2)
    {
        parent::__construct($message, intval($code));

        $this->raw = $raw;
    }
}
