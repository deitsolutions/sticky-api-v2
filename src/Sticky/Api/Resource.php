<?php
/**
 * @link https://github.com/deitsolutions/sticky-api-v2
 * @copyright Copyright (c) 2020 Almeyda LLC
 *
 * The full copyright and license information is stored in the LICENSE file distributed with this source code.
 */

namespace Sticky\Api;

class Resource
{
    /**
     * @var \stdClass
     */
    protected $fields;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $fieldMap = array();

    public function __construct($object = false)
    {
        if (is_array($object)) {
            $object = (isset($object[0])) ? $object[0] : false;
        }
        $this->fields = ($object) ? $object : new \stdClass;
        $this->id = ($object && isset($object->id)) ? $object->id : 0;
    }

    public function __get($field)
    {
        // first, find the field we should actually be examining
        $fieldName = isset($this->fieldMap[$field]) ? $this->fieldMap[$field] : $field;
        // then, if a method exists for the specified field and the field we should actually be examining
        // has a value, call the method instead
        if (method_exists($this, $field) && isset($this->fields->$fieldName)) {
            return $this->$field();
        }
        // otherwise, just return the field directly (or null)
        return (isset($this->fields->$field)) ? $this->fields->$field : null;
    }

    public function __set($field, $value)
    {
        $this->fields->$field = $value;
    }

    public function __isset($field)
    {
        return (isset($this->fields->$field));
    }
    
    public static function hashId($id)
    {
        return md5($id);
    }

}
