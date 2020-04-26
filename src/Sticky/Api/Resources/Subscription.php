<?php
/**
 * @link https://github.com/deitsolutions/sticky-api-v2
 * @copyright Copyright (c) 2020 Almeyda LLC
 *
 * The full copyright and license information is stored in the LICENSE file distributed with this source code.
 */
namespace Sticky\Api\Resources;

use Sticky\Api\Resource;
use Sticky\Api\Client;

/**
 * Class Subscription
 * @package Sticky\Api\Resources
 */
class Subscription extends Resource
{
    /**
     * Get
     * @param $id
     * @return mixed
     */
    public static function get($id)
    {
        return Client::getResource('/subscriptions/' . $id);
    }

    /**
     * Start
     * @param $id
     * @return mixed
     */
    public static function start($id)
    {
        return Client::updateResource('/subscriptions/' . $id . '/start');
    }

    /**
     * Stop
     * @param $id
     * @return mixed
     */
    public static function stop($id)
    {
        return Client::createResource('/subscriptions/' . $id . '/stop');
    }

    /**
     * Reset
     * @param $id
     * @return mixed
     */
    public static function reset($id)
    {
        return Client::createResource('/subscriptions/' . $id . '/reset');
    }

    /**
     *  Stop On Next Success
     * @param $id
     * @return mixed
     */
    public static function stopNext($id)
    {
        return Client::updateResource('/subscriptions/' . $id . '/terminate_next');
    }

    /**
     *  Destroy Stop On Next Success
     * @param $id
     * @return mixed
     */
    public static function destroyNextStop($id)
    {
        return Client::deleteResource('/subscriptions/' . $id . '/terminate_next');
    }
}