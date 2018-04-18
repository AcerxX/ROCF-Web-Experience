<?php

namespace App\Controller;


use App\Security\WebserviceUser;
use App\Service\ApiService;
use function print_r;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExploreProjectsController extends Controller
{
    public function explore(ApiService $apiService)
    {
        /** @var WebserviceUser $user */
        $user = $this->getUser();

        $parameterBag = [];

        if ($user instanceof WebserviceUser) {
            $parameterBag['user_id'] = $user->getId();
        }

        $projects = $apiService->callProjectsEngineApi(ApiService::ROUTE_PE_GET_PROJECTS_LISTING, $parameterBag);

        return $this->render(
            'Explore/explore.html.twig',
            [
                'user' => $user,
                'projects' => $projects['data']
            ]
        );
    }
}