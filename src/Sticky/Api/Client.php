<?php
/**
 * @link https://github.com/deitsolutions/sticky-api-v2
 * @copyright Copyright (c) 2020 Almeyda LLC
 *
 * The full copyright and license information is stored in the LICENSE file distributed with this source code.
 */

namespace Sticky\Api;

use \Exception as Exception;

/**
 * Sticky API Client.
 */
class Client
{
    /**
     * Username to connect to the API with
     *
     * @var string
     */
    private static $username;

    /**
     * API key
     *
     * @var string
     */
    private static $api_key;

    /**
     * Connection instance
     *
     * @var Connection
     */
    private static $connection;

    /**
     * Resource class name
     *
     * @var string
     */
    private static $resource;

    /**
     * API path prefix to be added to URL for requests
     *
     * @var string
     */
    private static $path_prefix = '/api/v2';

    /**
     * Full URL path to the configured API.
     *
     * @var string
     */
    public static $api_path;
    private static $client_id;
    private static $auth_token;
    private static $client_secret;
    private static $api_url = 'https://dnvbdemo.sticky.io';
    private static $login_url = 'https://dnvbdemo.sticky.io';

    /**
     * Configure the API client with the required settings to access
     * the API.
     *
     * Accepts OAuth and (for now!) Basic Auth credentials
     *
     * @param array $settings
     */
    public static function configure($settings)
    {
        if (isset($settings['client_id'])) {
            self::configureOAuth($settings);
        } else {
            self::configureBasicAuth($settings);
        }
    }

    /**
     * Configure the API client with the required OAuth credentials.
     *
     * Requires a settings array to be passed in with the following keys:
     *
     * - client_id
     * - auth_token
     * - api_url
     *
     * @param array $settings
     * @throws \Exception
     */
    public static function configureOAuth($settings)
    {
        if (!isset($settings['api_url'])) {
            throw new Exception("'api_url' must be provided");
        }

        if (!isset($settings['auth_token'])) {
            throw new Exception("'auth_token' must be provided");
        }

        self::$client_id = $settings['client_id'];
        self::$auth_token = $settings['auth_token'];

        self::$client_secret = isset($settings['client_secret']) ? $settings['client_secret'] : null;

        self::$api_url = $settings['api_url'];
        self::$api_path = self::$api_url . self::$path_prefix;

        self::$connection = false;
    }

    /**
     * Configure the API client with the required credentials.
     *
     * Requires a settings array to be passed in with the following keys:
     *
     * - api_url
     * - username
     * - api_key
     *
     * @param array $settings
     * @throws \Exception
     */
    public static function configureBasicAuth(array $settings)
    {
        if (!isset($settings['api_url'])) {
            throw new Exception("'api_url' must be provided");
        }

        if (!isset($settings['username'])) {
            throw new Exception("'username' must be provided");
        }

        if (!isset($settings['password'])) {
            throw new Exception("'password' must be provided");
        }

        self::$api_url = $settings['api_url'];
        self::$username = $settings['username'];
        self::$api_key = $settings['password'];
        self::$api_path = self::$api_url . self::$path_prefix;
        self::$connection = false;
    }

    /**
     * Configure the API client to throw exceptions when HTTP errors occur.
     *
     * Note that network faults will always cause an exception to be thrown.
     *
     * @param bool $option sets the value of this flag
     */
    public static function failOnError($option = true)
    {
        self::connection()->failOnError($option);
    }

    /**
     * Return XML strings from the API instead of building objects.
     */
    public static function useXml()
    {
        self::connection()->useXml();
    }

    /**
     * Return JSON objects from the API instead of XML Strings.
     * This is the default behavior.
     */
    public static function useJson()
    {
        self::connection()->useXml(false);
    }

    /**
     * Switch SSL certificate verification on requests.
     *
     * @param bool $option sets the value of this flag
     */
    public static function verifyPeer($option = false)
    {
        self::connection()->verifyPeer($option);
    }

    /**
     * Connect to the internet through a proxy server.
     *
     * @param string $host host server
     * @param int|bool $port port number to use, or false
     */
    public static function useProxy($host, $port = false)
    {
        self::connection()->useProxy($host, $port);
    }

    /**
     * Get error message returned from the last API request if
     * failOnError is false (default).
     *
     * @return string
     */
    public static function getLastError()
    {
        return self::connection()->getLastError();
    }

    /**
     * Get an instance of the HTTP connection object. Initializes
     * the connection if it is not already active.
     *
     * @return Connection
     */
    private static function connection()
    {
        if (!self::$connection) {
            self::$connection = new Connection();
            if (self::$client_id) {
                self::$connection->authenticateOauth(self::$client_id, self::$auth_token);
            } else {
                self::$connection->authenticateBasic(self::$username, self::$api_key);
            }
        }

        return self::$connection;
    }

    /**
     * Convenience method to return instance of the connection
     *
     * @return Connection
     */
    public static function getConnection()
    {
        return self::connection();
    }

    /**
     * Set the HTTP connection object. DANGER: This can screw up your Client!
     *
     * @param Connection $connection The connection to use
     */
    public static function setConnection(Connection $connection = null)
    {
        self::$connection = $connection;
    }

    /**
     * Get a collection result from the specified endpoint.
     *
     * @param string $path api endpoint
     * @param string $resource resource class to map individual items
     * @return mixed array|string mapped collection or XML string if useXml is true
     */
    public static function getCollection($path, $resource = 'Resource')
    {
        $response = self::connection()->get(self::$api_path . $path);

        return self::mapCollection($resource, $response);
    }

    /**
     * Get a resource entity from the specified endpoint.
     *
     * @param string $path api endpoint
     * @param string $resource resource class to map individual items
     * @return mixed Resource|string resource object or XML string if useXml is true
     */
    public static function getResource($path, $resource = 'Resource')
    {
        $response = self::connection()->get(self::$api_path . $path);

        return self::mapResource($resource, $response);
    }

    /**
     * Get a count value from the specified endpoint.
     *
     * @param string $path api endpoint
     * @return mixed int|string count value or XML string if useXml is true
     */
    public static function getCount($path)
    {
        $response = self::connection()->get(self::$api_path . $path);

        if ($response == false || is_string($response)) {
            return $response;
        }

        return $response->count;
    }

    /**
     * Send a post request to create a resource on the specified collection.
     *
     * @param string $path api endpoint
     * @param mixed $object object or XML string to create
     * @return mixed
     */
    public static function createResource($path, $object = [])
    {
        if (is_array($object)) {
            $object = (object)$object;
        }

        return self::connection()->post(self::$api_path . $path, $object);
    }

    /**
     * Send a put request to update the specified resource.
     *
     * @param string $path api endpoint
     * @param mixed $object object or XML string to update
     * @return mixed
     */
    public static function updateResource($path, $object = [])
    {
        if (is_array($object)) {
            $object = (object)$object;
        }

        return self::connection()->put(self::$api_path . $path, $object);
    }

    /**
     * Send a delete request to remove the specified resource.
     *
     * @param string $path api endpoint
     * @return mixed
     */
    public static function deleteResource($path)
    {
        return self::connection()->delete(self::$api_path . $path);
    }

    /**
     * Internal method to wrap items in a collection to resource classes.
     *
     * @param string $resource name of the resource class
     * @param array $object object collection
     * @return array
     */
    private static function mapCollection($resource, $object)
    {
        if ($object == false || is_string($object)) {
            return $object;
        }

        $baseResource = __NAMESPACE__ . '\\' . $resource;
        self::$resource = (class_exists($baseResource)) ? $baseResource : 'Sticky\\Api\\Resources\\' . $resource;

        return array_map(array('self', 'mapCollectionObject'), $object);
    }

    /**
     * Callback for mapping collection objects resource classes.
     *
     * @param \stdClass $object
     * @return Resource
     */
    private static function mapCollectionObject($object)
    {
        $class = self::$resource;

        return new $class($object);
    }

    /**
     * Map a single object to a resource class.
     *
     * @param string $resource name of the resource class
     * @param \stdClass $object
     * @return Resource
     */
    private static function mapResource($resource, $object)
    {
        if ($object == false || is_string($object)) {
            return $object;
        }

        $baseResource = __NAMESPACE__ . '\\' . $resource;
        $class = (class_exists($baseResource)) ? $baseResource : 'Sticky\\Api\\Resources\\' . $resource;
        return new $class($object);
    }

    /**
     * Map object representing a count to an integer value.
     *
     * @param \stdClass $object
     * @return int
     */
    private static function mapCount($object)
    {
        if ($object == false || is_string($object)) {
            return $object;
        }

        return $object->count;
    }

    /**
     * Swaps a temporary access code for a long expiry auth token.
     *
     * @param \stdClass|array $object
     * @return \stdClass
     */
    public static function getAuthToken($object)
    {
        $context = array_merge(array('grant_type' => 'authorization_code'), (array)$object);
        $connection = new Connection();

        return $connection->post(self::$login_url . '/oauth2/token', $context);
    }
}
