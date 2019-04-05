<?php


namespace Amap\Providers;


use Amap\Kernel\Config;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class BaseProviders
{
    const API_V3_URL = 'http://restapi.amap.com/v3/';

    const API_V4_URL = 'http://restapi.amap.com/v4/';

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 签名
     * @param array $data
     * @return string
     */
    protected function signature($data = [])
    {
        ksort($data, SORT_STRING);
        $tmpStr = '';
        foreach ($data as $key => $value) {
            if (strlen($tmpStr) == 0) {
                $tmpStr .= $key . "=" . $value;
            } else {
                $tmpStr .= "&" . $key . "=" . $value;
            }
        }
        $tmpStr .= $this->config['private_key'];
        $signStr = md5($tmpStr);
        return $signStr;
    }

    /**
     * 将数组中的bool值转为string
     * @param array $params
     * @return array
     */
    protected function dealParams(array $params)
    {
        foreach ($params as $index => $value) {
            if (is_bool($value)) {
                $params[$index] = $value ? 'true' : 'false';
            } elseif (is_array($value)) {
                $params[$index] = $this->dealParams($value);
            }
        }
        return $params;
    }

    /**
     * Make a get request.
     * @param string $endpoint
     * @param array $query
     * @param array $headers
     * @return array
     */
    protected function get($endpoint, $query = [], $headers = [])
    {
        return $this->request('get', $endpoint, [
            'headers' => $headers,
            'query' => $query,
        ]);
    }

    /**
     * Make a http request.
     * @param string $method
     * @param string $endpoint
     * @param array $options http://docs.guzzlephp.org/en/latest/request-options.html
     * @return array
     */
    protected function request($method, $endpoint, $options = [])
    {
        return $this->unwrapResponse($this->getHttpClient($this->getBaseOptions())->{$method}($endpoint, $options));
    }

    /**
     * Convert response contents to json.
     * @param ResponseInterface $response
     * @return ResponseInterface|array|string
     */
    protected function unwrapResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();
        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents)), true);
        }
        return $contents;
    }

    /**
     * Return http client.
     * @param array $options
     * @return Client
     */
    protected function getHttpClient(array $options = [])
    {
        return new Client($options);
    }

    /**
     * Return base Guzzle options.
     * @return array
     */
    protected function getBaseOptions()
    {
        $options = [
            'base_uri' => $this->getBaseUri(),
            'timeout' => method_exists($this, 'getTimeout') ? $this->getTimeout() : 5.0,
        ];
        return $options;
    }

    protected function getBaseUri()
    {
        return self::API_V3_URL;
    }

    /**
     * Make a post request.
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return array
     */
    protected function post($endpoint, $params = [], $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'form_params' => $params,
        ]);
    }

    /**
     * Make a post request with json params.
     * @param       $endpoint
     * @param array $params
     * @param array $headers
     * @return array
     */
    protected function postJson($endpoint, $params = [], $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'json' => $params,
        ]);
    }
}