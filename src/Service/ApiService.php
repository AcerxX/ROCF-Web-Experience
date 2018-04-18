<?php

namespace App\Service;

use App\Exception\RecaptchaException;

class ApiService
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    /**
     * User Engine Routes
     */
    public const ROUTE_UE_LOGIN = '/login';
    public const ROUTE_UE_REGISTER = '/register';
    public const ROUTE_UE_RESET_PASSWORD = '/reset-password';
    public const ROUTE_UE_EDIT_PROFILE = '/edit-profile';
    public const ROUTE_UE_CHECK_TOKEN = '/check-token';

    /**
     * Projects Engine Routes
     */
    public const ROUTE_PE_CREATE_PROJECT = '/create-project';
    public const ROUTE_PE_GET_PROJECT_INFO = '/get-project-info';
    public const ROUTE_PE_UPDATE_PROJECT_INFO = '/update-project-info';
    public const ROUTE_PE_REMOVE_PROJECT = '/remove-project';
    public const ROUTE_PE_ADD_PERK = '/add-perk';
    public const ROUTE_PE_UPDATE_PERK_INFO = '/update-perk-info';
    public const ROUTE_PE_REMOVE_PERK = '/remove-perk';
    public const ROUTE_PE_GET_CATEGORIES = '/get-categories';
    public const ROUTE_PE_GET_PROJECTS_LISTING = '/get-projects-listing';

    /**
     * @var string
     */
    private $usersEngineHost;

    /**
     * @var string
     */
    private $projectsEngineHost;

    public function __construct(string $usersEngineHost, string $projectsEngineHost)
    {
        $this->usersEngineHost = $usersEngineHost;
        $this->projectsEngineHost = $projectsEngineHost;
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
    public function callProjectsEngineApi(string $route, array $requestBag = [], string $method = 'POST'): array
    {
        return $this->callApi($this->projectsEngineHost . $route, $requestBag, $method);
    }


    /**
     * @param string $route
     * @param array $requestBag
     * @param string $method
     * @return array
     * @throws \InvalidArgumentException
     */
    public function callApi(string $route, array $requestBag = [], string $method = 'POST'): array
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
    }

    /**
     * @param string $response
     * @param string $serverAddress
     * @throws RecaptchaException
     */
    public function checkRecaptcha(string $response, string $serverAddress): void
    {
        $secretKey = '6Lf88kIUAAAAALNYkGr1L4t_N594TyZdrYGDLQUz';
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $return = file_get_contents(
            $url
            . '?secret='
            . $secretKey
            . '&response='
            . $response
            . '&remoteip='
            . $serverAddress
        );
        $data = json_decode($return);

        if (!isset($data->success) || $data->success === false) {
            throw new RecaptchaException('captcha.error');
        }
    }
}
