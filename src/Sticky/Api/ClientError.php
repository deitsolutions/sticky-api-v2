<?php
/**
 * @link https://github.com/deitsolutions/sticky-api-v2
 * @copyright Copyright (c) 2020 Almeyda LLC
 *
 * The full copyright and license information is stored in the LICENSE file distributed with this source code.
 */

namespace Sticky\Api;

/**
 * Raised when a client error (400+) is returned from the API.
 */
class ClientError extends Error
{
    public function __toString()
    {
        return "Client Error ({$this->code}): " . $this->message;
    }
}
