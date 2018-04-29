<?php

namespace App\Controller;


use App\Security\WebserviceUser;
use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

        $categories = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_GET_CATEGORIES,
            [],
            ApiService::METHOD_GET
        );

        return $this->render(
            'Explore/explore.html.twig',
            [
                'user' => $user,
                'projects' => $projects['data'],
                'categories' => $categories
            ]
        );
    }


    public function search(Request $request, ApiService $apiService)
    {
        $parameterBag = [];

        $searchedText = $request->get('searched_text');
        if (null !== $searchedText) {
            $parameterBag['searched_text'] = $searchedText;
        }

        $categoryId = $request->get('category_id');
        if (null !== $categoryId) {
            $parameterBag['category_id'] = $categoryId;
        }

        $projects = $apiService->callProjectsEngineApi(ApiService::ROUTE_PE_GET_PROJECTS_LISTING, $parameterBag);

        $categories = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_GET_CATEGORIES,
            [],
            ApiService::METHOD_GET
        );

        return $this->render(
            'Explore/searchResults.html.twig',
            [
                'user' => $this->getUser(),
                'projects' => $projects['data'],
                'searchedText' => $searchedText,
                'searchedCategoryId' => $categoryId,
                'categories' => $categories
            ]
        );
    }
}