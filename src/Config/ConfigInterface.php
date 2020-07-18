<?php

namespace Fruitware\WhmcsWrapper\Config;

/**
 * @package Fruitware\WhmcsWrapper
 * @author   Fruits Foundation <foundation@fruits.agency>
 */
interface ConfigInterface
{
    /**
     * ConfigInterface constructor.
     * Protected to disable instancing but static i() method
     *
     *
     * @param  string  $apiBaseUri
     * @param  string  $key
     * @param  string  $secret
     * @param  array  $postDefaultParams
     * @param  array  $clientOptions
     */
    public function __construct(
        string $apiBaseUri,
        string $key,
        string $secret,
        array $postDefaultParams,
        array $clientOptions
    );

    /**
     * @param  string  $baseUri  path to your WHMCS installation (HTTP_ROOT)
     * @param  string  $identifier  WHMCS Identifier
     * @param  string  $secret  WHMCS Identifier's Secret key
     * @param  array|string[]  $defaultConfig  default: array['responsetype' => 'json']
     * @param  array  $clientOptions  Request options to apply. See \GuzzleHttp\RequestOptions
     *
     * @return ConfigInterface
     */
    public static function i(
        string $baseUri,
        string $identifier,
        string $secret,
        array $defaultConfig = [],
        array $clientOptions = []
    ):
    ConfigInterface;

    public function getRequestUrl(): string;

    /**
     * GuzzleHttp\ClientInterface client's config (used during initialization)
     * @return array
     */
    public function prepareConfig(): array;

    /**
     * Base64 encoded serialized array of custom field values
     * @link https://developers.whmcs.com/api-reference/addorder/
     * @link http://mahmudulruet.blogspot.com/2011/10/adding-and-posting-custom-field-values.html
     *
     * 1. Login to your WHMCS admin panel.
     * 2. Navigate Setup->Client Custom Fields
     * 3. If there is no custom fields yet create a new one; say named "VAT".
     * 4. After creating the field, see the HTML source from the page by right clicking on page and click view source (in Firefox).
     * 5. Find the text "VAT".
     * 6. You will find something like this line of HTML code (<input type="text" size="30" value="VAT" name="fieldname[11]">)
     * 7. This fieldname[] with id may vary by users admin panel. So track the id from fieldname[11] and yes it's 11. If you find something like <input type="text" size="30" value="VAT" name="fieldname[xx]"> then 'xx' will be the id you have to use in your $customfields array. The array now should look like:
     *
     *      $customfields = [
     *          "11" => "123456",
     *          "12" => "678945"
     *      ];
     *      $postfields["customfields"] = base64_encode(serialize($customfields));
     *
     * Preprocessing the request params.
     * Main purpose of the method is to encode `customfields` param.
     *
     * @param  string  $action  method name
     * @param  array  $params  query params
     * @param  bool  $skipValidation  cancel validation of the required fields
     *
     * @return array
     */
    public function prepareRequestOptions(string $action, array $params, bool $skipValidation): array;
}