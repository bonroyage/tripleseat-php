<?php

namespace Tripleseat\Http;

use ApiClients\Tools\Psr7\Oauth1\Definition\ConsumerKey;
use ApiClients\Tools\Psr7\Oauth1\Definition\ConsumerSecret;
use ApiClients\Tools\Psr7\Oauth1\RequestSigning\RequestSigner;
use ApiClients\Tools\Psr7\Oauth1\Signature\HmacSha1Signature;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use JsonException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Tripleseat\Contracts\Http;
use Tripleseat\Exceptions\HttpException;

class Client implements Http
{
    protected ClientInterface $http;

    protected RequestFactoryInterface $requestFactory;

    protected StreamFactoryInterface $streamFactory;

    protected array $headers = [
        'Content-type' => 'application/json',
    ];

    protected string $baseUrl = 'https://api.tripleseat.com/v1/';

    protected RequestSigner $requestSigner;

    public function __construct(
        protected array $auth = [],
        ?ClientInterface $httpClient = null,
    ) {
        $this->http = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $consumerSecret = new ConsumerSecret(
            consumerSecret: $auth['secret_key']
        );

        $this->requestSigner = new RequestSigner(
            consumerKey: new ConsumerKey(
                consumerKey: $auth['api_key']
            ),
            consumerSecret: $consumerSecret,
            signature: new HmacSha1Signature(
                consumerSecret: $consumerSecret
            )
        );
    }

    protected function createRequest(string $method, string $path, array $query = []): RequestInterface
    {
        if (isset($this->auth['site_id'])) {
            $query['site_id'] = $this->auth['site_id'];
        }

        return $this->requestFactory->createRequest(
            method: $method,
            uri: $this->baseUrl . $path . $this->buildQueryString($query)
        );
    }

    protected function buildQueryString(array $queryParams = []): string
    {
        return $queryParams ? '?' . http_build_query($queryParams) : '';
    }

    protected function execute(RequestInterface $request): array
    {
        foreach ($this->headers as $header => $value) {
            $request = $request->withAddedHeader($header, $value);
        }

        $request = $this->requestSigner->sign(
            request: $request
        );

        return $this->parseResponse(
            response: $this->http->sendRequest($request)
        );
    }

    protected function parseResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() >= 300) {
            try {
                $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $body = $response->getReasonPhrase();
            }

            throw new HttpException(
                httpStatus: $response->getStatusCode(),
                httpBody: $body
            );
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function get(string $path, array $query = []): array
    {
        $request = $this->createRequest(
            method: 'GET',
            path: $path,
            query: $query
        );

        return $this->execute(
            request: $request
        );
    }

    public function post(string $path, ?array $body = null, array $query = []): array
    {
        $request = $this->createRequest(
            method: 'POST',
            path: $path,
            query: $query
        )->withBody($this->streamFactory->createStream(json_encode($body)));

        return $this->execute($request);
    }

    public function put(string $path, ?array $body = null, array $query = []): array
    {
        $request = $this->createRequest(
            method: 'PUT',
            path: $path,
            query: $query
        )->withBody($this->streamFactory->createStream(json_encode($body)));

        return $this->execute($request);
    }

    public function delete(string $path, array $query = []): array
    {
        $request = $this->createRequest(
            method: 'DELETE',
            path: $path,
            query: $query
        );

        return $this->execute($request);
    }

    public function publicKey(): string
    {
        return $this->auth['public_key'];
    }
}
