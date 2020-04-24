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
 * Class Product
 * @package Sticky\Api\Resources
 */
class Product extends Resource
{
    /**
     * Get
     * @param $id
     * @return mixed
     */
    public static function all()
    {
        return Client::getCollection('/products');
    }

    /**
     * Get
     * @param $id
     * @return mixed
     */
    public static function get($id)
    {
        return Client::getResource('/products/' . $id);
    }

    /**
     * Add
     * @param $id
     * @param $object
     * @return mixed
     */
    public static function add($object)
    {
        return Client::createResource('/products', $object);
    }

    /**
     * Update
     * @param $id
     * @param $object
     * @return mixed
     */
    public static function update($id, $object)
    {
        return Client::updateResource('/products/' . $id, $object);
    }

    /**
     * Delete
     * @param $id
     * @return mixed
     */
    public static function delete($id)
    {
        return Client::deleteResource('/products/' . $id);
    }

}