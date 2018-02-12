<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 01.02.2018
 * Time: 21:09
 */

namespace App\Security;

use App\Security\WebserviceUser;
use App\Service\ApiService;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class WebserviceUserProvider implements UserProviderInterface
{
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @param string $username
     * @return \App\Security\WebserviceUser|UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        // make a call to your webservice here
        try {
            $userData = $this->apiService->callUsersEngineApi(
                ApiService::ROUTE_UE_LOGIN,
                [
                    'email' => $username
                ]
            );
        } catch (\InvalidArgumentException $e) {
            $userData['isError'] = true;
        }

        if ($userData['isError'] === false) {
            $password = $userData['userInformation']['password'];
            $salt = $userData['userInformation']['salt'];
            $roles = $userData['userInformation']['roles'];
            $firstName = $userData['userInformation']['firstName'];
            $lastName = $userData['userInformation']['lastName'];
            $id = $userData['userInformation']['userId'];

            return new WebserviceUser($username, $password, $salt, $roles, $firstName, $lastName, $id);
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    /**
     * @param UserInterface $user
     * @return \App\Security\WebserviceUser|UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class): bool
    {
        return WebserviceUser::class === $class;
    }
}
