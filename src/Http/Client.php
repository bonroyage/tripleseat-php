<?php namespace Tripleseat\Http;

use ApiClients\Tools\Psr7\Oauth1\Definition\ConsumerKey;
use ApiClients\Tools\Psr7\Oauth1\Definition\ConsumerSecret;
use ApiClients\Tools\Psr7\Oauth1\RequestSigning\RequestSigner;
use ApiClients\Tools\Psr7\Oauth1\Signature\HmacSha1Signature;
use Generator;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Tripleseat\Contracts\Http;
use Tripleseat\Exceptions\HttpException;

class Client implements Http
{

    /**
     * @var Http
     */
    private $http;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var RequestSigner
     */
    private $requestSigner;

    /**
     * @var array
     */
    private $auth;

    /**
     * Client constructor.
     *
     * @param array $auth
     * @param ClientInterface|null $httpClient
     */
    public function __construct(array $auth = [], ClientInterface $httpClient = null)
    {
        $this->auth = $auth;
        $this->baseUrl = 'https://api.tripleseat.com/v1/';

        $this->http = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $consumerSecret = new ConsumerSecret($auth['secret_key']);
        $this->requestSigner = new RequestSigner(
            new ConsumerKey($auth['api_key']),
            $consumerSecret,
            new HmacSha1Signature($consumerSecret)
        );

        $this->headers = array_filter([
            'Content-type' => 'application/json',
        ]);
    }

    public function getPaged(string $path, array $query = [], int $fromPage = 1, int $untilPage = PHP_INT_MAX): Generator
    {
        // Initial page number cannot be less than 1
        $fromPage = max(1, $fromPage);

        // Last page number cannot be less than the initial page number
        $untilPage = max($untilPage, $fromPage);

        $page = $fromPage;
        while ($page <= $untilPage) {
            $data = $this->get($path, array_merge($query, ['page' => $page]));
            $untilPage = min($untilPage, isset($data['total_pages']) ? $data['total_pages'] : 1);
            $results = $data['results'];

            foreach ($results as $result) {
                yield $result;
            }

            $page++;
        }
    }

    /**
     * @param string $path
     * @param array $query
     *
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws HttpException
     */
    public function get(string $path, array $query = [])
    {
        $request = $this->createRequest('GET', $path, $query);

        return $this->execute($request);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $query
     * @return RequestInterface
     */
    public function createRequest(string $method, string $path, array $query = []): RequestInterface
    {
        if (isset($this->auth['site_id'])) {
            $query['site_id'] = $this->auth['site_id'];
        }

        return $this->requestFactory->createRequest(
            $method,
            sprintf("%s%s%s", $this->baseUrl, $path, $this->buildQueryString($query))
        );
    }

    private function buildQueryString(array $queryParams = []): string
    {
        return $queryParams ? '?' . http_build_query($queryParams) : '';
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws HttpException
     */
    private function execute(RequestInterface $request)
    {
        foreach ($this->headers as $header => $value) {
            $request = $request->withAddedHeader($header, $value);
        }

        $request = $this->requestSigner->sign($request);

        return $this->parseResponse($this->http->sendRequest($request));
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     *
     * @throws HttpException
     */
    private function parseResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() >= 300) {
            $body = json_decode($response->getBody()->getContents(), true) ?? $response->getReasonPhrase();
            throw new HttpException($response->getStatusCode(), $body);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $path
     * @param mixed $body
     * @param array $query
     *
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws HttpException
     */
    public function post(string $path, $body = null, array $query = [])
    {
        $request = $this->createRequest('POST', $path, $query)
            ->withBody($this->streamFactory->createStream(json_encode($body)));

        return $this->execute($request);
    }

    /**
     * @param string $path
     * @param mixed $body
     * @param array $query
     *
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws HttpException
     */
    public function put(string $path, $body = null, array $query = [])
    {
        $request = $this->createRequest('PUT', $path, $query)
            ->withBody($this->streamFactory->createStream(json_encode($body)));

        return $this->execute($request);
    }

    /**
     * @param $path
     * @param array $query
     *
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws HttpException
     */
    public function delete(string $path, array $query = [])
    {
        $request = $this->createRequest('DELETE', $path, $query);

        return $this->execute($request);
    }

    /**
     * @param string|null $key
     * @return array|string|null
     */
    public function getAuth(string $key = null)
    {
        if (is_null($key)) {
            return $this->auth;
        }

        return isset($this->auth[$key]) ? $this->auth[$key] : null;
    }

}