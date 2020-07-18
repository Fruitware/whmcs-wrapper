<?php

namespace Fruitware\WhmcsWrapper\Connect;

use Exception;
use Fruitware\WhmcsWrapper\Config\ConfigInterface;
use Fruitware\WhmcsWrapper\Exception\RuntimeException;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\AggregateException;

/**
 * @todo: morph connector into the
 *
 * Class Connector
 *
 * @package Fruitware\WhmcsWrapper
 */
final class Connector
{
    /**
     * WHMCS API wrapper
     *
     * @example https://github.com/PeteBishwhip/WHMCSAPI
     */
    private ?Client $client;

    protected string $whmcsUri;
    protected string $whmcsIdentifier;
    protected string $whmcsSecret;

    /** @var array http-client options */
    protected array $requestOptions;

    /**
     * @var array params automatically used for each api-call
     */
    protected array $defaultParams;

    /** @var string Request url (path) for any api call */
    protected string $requestUrl;

    /**
     * Protected constructor
     * Doesn't supposed to be called directly
     *
     * @param  ConfigInterface  $config
     */
    protected function __construct(ConfigInterface $config)
    {
        $this->whmcsUri = $config->getBaseUri();
        $this->whmcsIdentifier = $config->getAuthIdentifier();
        $this->whmcsSecret = $config->getAuthSecret();
        $this->defaultParams = $config->getDefaultParams();
        $this->requestUrl = $config->getRequestUrl();
        $this->requestOptions = $config->getRequestOptions();
    }

    /**
     * To avoid direct calls to the client are
     * @return Client
     */
    protected function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client(
                array_merge(
                    $this->defaultParams,
                    $this->requestOptions
                )
            );
        }
        return $this->client;
    }

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
    protected function filterParams(string $action, array $params, bool $skipValidation): array
    {
        if ($skipValidation) {
            $params['skipvalidation'] = true;
        }
        $params['action'] = $action;
        $result = array_merge($this->defaultParams, $params);

        if (!empty($result['customfields'])) {
            $result['customfields'] = base64_encode(serialize($result['customfields']));
        }
        return [
            'form_params' => $result
        ];
    }

    /**
     * Get servers. In addition tries to fetch status. Void
     *
     * @throws RuntimeException
     */
    public function validate(): void
    {
        $response = $this->call('GetServers', ['fetchStatus' => true]);

        if (!$response || $response['result'] !== 'success') {
            throw new RuntimeException('Server is done!', 500);
        }
    }

    /**
     * Main method and only one available (excluding the constructor)
     *
     * @link https://developers.whmcs.com/api-reference/addclient/
     *
     * @param  string  $action
     * @param  array  $params
     * @param  bool  $skipValidation
     *
     * @return array
     * @throws RuntimeException
     */
    public function call(string $action, array $params = [], bool $skipValidation = false): ?array
    {
        try {
            $response = $this->getClient()->post(
                $this->whmcsUri,
                $this->filterParams(
                    $action,
                    $params,
                    $skipValidation
                )
            );

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('Code not 200: '.$response->getReasonPhrase(), $response->getStatusCode());
            }

            return json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR | JSON_THROW_ON_ERROR
            );
        } catch (AggregateException $e) {
            throw new RuntimeException('GuzzleException:'.$e->getMessage(), $e->getCode());

        } catch (Exception $e) {
            throw new RuntimeException('JsonException:'.$e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param  ConfigInterface  $config
     * @return Connector
     *
     * @throws RuntimeException
     */
    public static function connect(ConfigInterface $config): Connector
    {
        $self = new self($config);
        $self->validate();

        return $self;
    }
}
