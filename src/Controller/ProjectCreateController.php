<?php

namespace App\Controller;

use App\Security\WebserviceUser;
use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProjectCreateController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \LogicException
     */
    public function chooseProjectType()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render(
            'Project/Create/chooseType.html.twig',
            [
                'user' => $this->getUser()
            ]
        );
    }


    /**
     * @param ApiService $apiService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function editProjectBasic(ApiService $apiService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /**
         * API Call Projects Engine to create a project for current logged in user.
         * DO NOT CHANGE THIS :)
         */
        /** @var WebserviceUser $user */
        $user = $this->getUser();
        $response = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_CREATE_PROJECT,
            [
                'user_id' => $user->getId()
            ]
        );

        if ($response['isError'] === true) {
            return $this->redirectToRoute('chooseProjectType');
        }

        // TODO: create the form in twig
        return $this->render(
            'Project/Create/editBasic.html.twig',
            [
                'user' => $user,
                'projectId' => $response['data']['id']
            ]
        );
    }


    public function saveProjectBasic(Request $request)
    {
        /**
         * TODO API CALL UPDATE PROJECT API WITH INPUT DATA
         */

        return;
    }


    /**
     * @param Request $request
     * @param string $projectLink
     * @param int $projectId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function editProject(
        Request $request,
        string $projectLink,
        int $projectId,
        ApiService $apiService
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $projectInfo = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_GET_PROJECT_INFO,
            [
                'id' => $projectId
            ]
        );

        if ($projectInfo['isError']) {
            /**
             * TODO: redirect to 404 page
             */
            print_r($projectInfo);
            die();
        }



        return $this->render(
            'Project/Create/editProject.html.twig',
            [
                'user' => $this->getUser(),
                'projectInfo' => $projectInfo['data'],
                'completedPercentage' =>
                    $projectInfo['data']['pledgedAmount'] * 100 / $projectInfo['data']['totalAmount']
            ]
        );
    }
}
