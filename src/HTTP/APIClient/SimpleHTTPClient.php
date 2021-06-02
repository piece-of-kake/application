<?php

namespace PoK\HTTP\APIClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use PoK\ValueObject\TypeURI;

class SimpleHTTPClient implements HTTPAPIClientInterface
{
    private $uri;
    private $authorizationStringFactory;

    public function __construct(TypeURI $uri)
    {
        $this->uri = $uri;
    }

    public function setAuthorizationStringFactory(callable $authorizationStringFactory)
    {
        $this->authorizationStringFactory = $authorizationStringFactory;
        return $this;
    }

    public function getURI()
    {
        return $this->uri;
    }

    public function post($data = [])
    {
        $method = strtoupper('POST');

        $options = [
            'headers' => ['Accept' => 'application/json']
        ];
        if (!empty($data))
            $options['form_params'] = $data;

        if (is_callable($this->authorizationStringFactory)) {
            $options['headers']['Authorization'] = ($this->authorizationStringFactory)();
        }

        $client = new Client();
        try {
            $response = $client->request(
                $method,
                $this->uri->getFullURI(),
                $options
            );
            $code = $response->getStatusCode();
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (ServerException | RequestException $e) {
            $code = $e->getResponse()->getStatusCode();
            $data = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return new APIClientResponse($code, $data);
    }

    public function put($data = [])
    {
        $method = strtoupper('PUT');

        $options = [
            'headers' => ['Accept' => 'application/json'],
            'query' => $data
        ];

        if (is_callable($this->authorizationStringFactory)) {
            $options['headers']['Authorization'] = ($this->authorizationStringFactory)();
        }

        $client = new Client();
        try {
            $response = $client->request(
                $method,
                $this->uri->getFullURI(),
                $options
            );
            $code = $response->getStatusCode();
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (ServerException | RequestException $e) {
            $code = $e->getResponse()->getStatusCode();
            $data = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return new APIClientResponse($code, $data);
    }

    public function get($data = [])
    {
        $method = strtoupper('GET');

        $options = [
            'headers' => ['Accept' => 'application/json'],
            'query' => $data
        ];

        if (is_callable($this->authorizationStringFactory)) {
            $options['headers']['Authorization'] = ($this->authorizationStringFactory)();
        }

        $client = new Client();
        try {
            $response = $client->request(
                $method,
                $this->uri->getFullURI(),
                $options
            );
            $code = $response->getStatusCode();
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (ServerException | RequestException $e) {
            $code = $e->getResponse()->getStatusCode();
            $data = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return new APIClientResponse($code, $data);
    }

    public function delete($data = [])
    {
        $method = strtoupper('DELETE');

        $options = [
            'headers' => ['Accept' => 'application/json']
        ];
        if (!empty($data))
            $this->uri->setQuery(http_build_query($data)); // because DELETE HTTP request ignores entity body

        if (is_callable($this->authorizationStringFactory)) {
            $options['headers']['Authorization'] = ($this->authorizationStringFactory)();
        }

        $client = new Client();
        try {
            $response = $client->request(
                $method,
                $this->uri->getFullURI(),
                $options
            );
            $code = $response->getStatusCode();
            $data = json_decode($response->getBody()->getContents(), true);
        } catch (ServerException | RequestException $e) {
            $code = $e->getResponse()->getStatusCode();
            $data = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

        return new APIClientResponse($code, $data);
    }
}