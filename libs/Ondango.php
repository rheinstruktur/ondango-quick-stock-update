<?php
/**
* Ondango PHP-SDK for Ondango API
* written by Claudio Bredfeldt & Antonio López Muzas
*
* http://github.com/Ondango/PHP-SDK
* http://apidocs.ondango.com
*
* Copyright (c) 2012 Ondango GmbH (http://ondango.com)
* Dual licensed under the MIT and GPL licenses.
*/
 
require_once dirname (__FILE__)."/OndangoRequest.php";

class Ondango
{
private $api_key = null;
private $api_secret = null;


public function __construct ($api_key, $api_secret = null)
{
$this->api_key = $api_key;
$this->api_secret = $api_secret;
}


/**
* Magic method for GET, PUT, POST or DELETE
*
* @param string $method GET, PUT, POST or DELETE
* @param array $args
* @return mixed
*/
public function __call ($method, $args)
{
$allowed_methods = array ("get", "put", "post", "delete");

if (!in_array (strtolower ($method), $allowed_methods)) {
die ("Fatal error: Call to undefined method Ondango::{$method}(). Only following magic methods are allowed: ".implode (", ", $allowed_methods));
}
else if (empty ($args[0])) {
die ("Fatal error: Missing argument 1 for Ondango::{$method}(). You have to provide a api url (see: http://apidocs.ondango.com");
}

return $this->request ($method, $args[0], $args[1]);
}

/**
* Compose and execute a specific api url (i.e: /shops?api_key=...&id=n)
*
* @param string $method GET, PUT, POST or DELETE
* @param string $url
* @param array $params [optional] i.e: array ("shop_id" => 5)
* @return object
*/
public function request ($method, $url, $params = array ())
{
$request = new OndangoRequest ($method, $url, $this->init_params ($params));

return json_decode ($request->execute ());
}

/**
* Add additional information to the parameters of the api url
* i.e: the API key and Secret key
*
* @param array $params
* @return array
*/
private function init_params ($params)
{
$params["api_key"] = $this->api_key;
$params["api_secret"] = $this->api_secret;

return $params;
}
}
?>