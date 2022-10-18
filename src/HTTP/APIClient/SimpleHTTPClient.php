<?php

namespace PoK\HTTP\APIClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use PoK\Exceptions\Internal\FailedHTTPRequestException;
use PoK\ValueObject\TypeURI;

class SimpleHTTPClient implements HTTPAPIClientInterface
{
    private $uri;
    private $authorizationStringFactory;
    private $verifySSL = true;

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

    public function skipSSL()
    {
        $this->verifySSL = false;
        return $this;
    }

    /**
     * @param array $data
     * @param array $files Array of PoK\HTTP\APIClient\File
     * @return APIClientResponse
     * @throws FailedHTTPRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function post(array $data = [], array $files = [])
    {
        $method = strtoupper('POST');

        $options = [
            'headers' => ['Accept' => 'application/json']
        ];

        if (!empty($files)) {
            $options['multipart'] = [];
            foreach ($files as $file) {
                /** @var File $file */
                $options['multipart'][] = $file->compile();
            }
            if (!empty($data))
                foreach ($data as $name => $value)
                    $options['multipart'][] = ['name' => $name, 'contents' => $value];
        }
        else if (!empty($data))
            $options['form_params'] = $data;

        if (is_callable($this->authorizationStringFactory)) {
            $options['headers']['Authorization'] = ($this->authorizationStringFactory)();
        }

        $client = new Client([
            RequestOptions::VERIFY => $this->verifySSL
        ]);
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
            $data = json_decode((string) $e->getResponse()->getBody(), true);
        } catch (\Throwable $exception) {
            // ToDo: this needs some incident info
            throw new FailedHTTPRequestException();
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

        $client = new Client([
            RequestOptions::VERIFY => $this->verifySSL
        ]);
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
            $data = json_decode((string) $e->getResponse()->getBody(), true);
        } catch (\Throwable $exception) {
            // ToDo: this needs some incident info
            throw new FailedHTTPRequestException();
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

        $client = new Client([
            RequestOptions::VERIFY => $this->verifySSL
        ]);
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
            $data = json_decode((string) $e->getResponse()->getBody(), true);
        } catch (\Throwable $exception) {
            // ToDo: this needs some incident info
            throw new FailedHTTPRequestException();
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

        $client = new Client([
            RequestOptions::VERIFY => $this->verifySSL
        ]);
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
            $data = json_decode((string) $e->getResponse()->getBody(), true);
        } catch (\Throwable $exception) {
            // ToDo: this needs some incident info
            throw new FailedHTTPRequestException();
        }

        return new APIClientResponse($code, $data);
    }
}