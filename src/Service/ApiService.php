<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class ApiService
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    public const ROUTE_UE_LOGIN = '/login';
    public const ROUTE_UE_REGISTER = '/register';
    public const ROUTE_UE_RESET_PASSWORD = '/reset-password';
    public const ROUTE_UE_EDIT_PROFILE = '/edit-profile';
    public const ROUTE_UE_CHECK_TOKEN = '/check-token';

    /**
     * @var string
     */
    private $usersEngineHost;

    public function __construct(string $usersEngineHost)
    {
        $this->usersEngineHost = $usersEngineHost;
    }


    /**
     * @param string $route
     * @param array $requestBag
     * @param string $method
     * @return array
     * @throws \InvalidArgumentException
     */
    public function callUsersEngineApi(string $route, array $requestBag = [], string $method = 'POST'): array
    {
        return $this->callApi($this->usersEngineHost . $route, $requestBag, $method);
    }

    /**
     * @param string $route
     * @param array $requestBag
     * @param string $method
     * @return array
     * @throws \InvalidArgumentException
     */
    private function callApi(string $route, array $requestBag = [], string $method = 'POST'): array
    {
        $this->validateInput($route, $requestBag, $method);

        // Get cURL resource
        $curl = curl_init();

        // Set some options - we are passing in an userAgent too here
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $route,
            CURLOPT_USERAGENT => 'Web Experience',
            CURLOPT_VERBOSE => true
        ];

        if ($method === self::METHOD_POST) {
            $message = json_encode($requestBag);
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $message;

            curl_setopt(
                $curl,
                CURLOPT_HTTPHEADER,
                [
                    'Content-Type: application/json',
                    'Content-Length: ' . \strlen($message)
                ]
            );
        }
        curl_setopt_array($curl, $options);

        // Send the request & save response to $resp
        $response = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);

        return json_decode($response, true);
    }

    /**
     * @param string $route
     * @param array $requestBag
     * @param string $method
     * @throws \InvalidArgumentException
     */
    private function validateInput(string $route, array $requestBag, string $method): void
    {
        if (empty($route)) {
            throw new \InvalidArgumentException('Route called should not be empty!');
        }

        if ($method === self::METHOD_POST && \count($requestBag) === 0) {
            throw new \InvalidArgumentException('The request bag should not be empty in an POST request!');
        }
    }
}
