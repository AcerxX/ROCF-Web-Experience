<?php

namespace App\Controller;

use App\Security\WebserviceUser;
use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use function var_dump;

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

        /**
         * TODO: uncomment this and remove redirect
         *
         * return $this->render(
         * 'Project/Create/editBasic.html.twig',
         * [
         * 'user' => $user,
         * 'projectId' => $response['data']['id']
         * ]
         * );
         * */
        return $this->redirectToRoute(
            'editProject',
            [
                'projectId' => $response['data']['id'],
                'projectLink' => $response['data']['link']
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
    )
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var WebserviceUser $loggedUser */
        $loggedUser = $this->getUser();

        $projectInfo = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_GET_PROJECT_INFO,
            [
                'id' => $projectId
            ]
        );


        if ($projectInfo['isError'] /*|| $projectInfo['data']['userId'] !== $loggedUser->getId()*/) {
            /**
             * TODO: redirect to 404 page
             */
            print_r($projectInfo);
            die();
        }
        $completedPercentage = 0;
        if (isset($projectInfo['data']['totalAmount'])) {
            $completedPercentage = $projectInfo['data']['pledgedAmount'] * 100 / $projectInfo['data']['totalAmount'];
        }

        return $this->render(
            'Project/Create/editProject.html.twig',
            [
                'user' => $loggedUser,
                'projectInfo' => $projectInfo['data'],
                'completedPercentage' => $completedPercentage
            ]
        );
    }

    /**
     * @param Request $request
     * @param ApiService $apiService
     * @return JsonResponse
     * @throws \InvalidArgumentException
     */
    public function deletePerk(Request $request, ApiService $apiService): JsonResponse
    {
        $projectId = $request->request->get('projectId');
        $perkInfo = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_ADD_PERK,
            [
                'project_id' => $projectId
            ]
        );

        $response = '';
        foreach ($perkInfo['data'] as $key => $perk) {
            $response .= '<div class="gallery-cell">
                            <div class="card" style="width: 18rem;">
                                <img class="card-img-top"
                                     src="../../../../build/keditor/snippets/default/img/yenbai_vietnam_squared.jpg"
                                     alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">' . $perk['title'] . '</h5>
                                    <p class="card-text">' . $perk['description'] . '</p>
                                    <a href="#" class="btn btn-primary">' . $perk['amount'] . '</a>
                                </div>
                            </div>
                        </div>';
        }

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @param ApiService $apiService
     * @throws \InvalidArgumentException
     */
    public function addPerk(Request $request, ApiService $apiService)
    {
        $projectId = $request->request->get('projectId');
        $perkInfo = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_ADD_PERK,
            [
                'project_id' => $projectId,
                'title' => 'Click aici pentru a edita titlul...',
                'amount' => '0',
                'description' => 'Descrierea recompensei...'
            ]
        );

        $response = '<div class="gallery-cell">
                            <div class="card" style="width: 18rem;">
                                <img class="card-img-top"
                                     src="../../../../build/keditor/snippets/default/img/yenbai_vietnam_squared.jpg"
                                     alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">' . $perkInfo['data']['title'] . '</h5>
                                    <p class="card-text">' . $perkInfo['data']['description'] . '</p>
                                    <a href="#" class="btn btn-primary">' . $perkInfo['data']['amount'] . ' RON</a>
                                </div>
                            </div>
                        </div>';

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @param ApiService $apiService
     * @return JsonResponse
     * @throws \InvalidArgumentException
     */
    public function autosaveProject(Request $request, ApiService $apiService): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $projectId = $request->get('projectId');

        $title = strip_tags($content['title']);
        $shortDescription = $content['shortDescription'];
        $content = $content['content'];

        $requestBag = [
            'project_id' => $projectId,
            'title' => $title,
            'short_description' => $shortDescription,
            'content' => $content
        ];

        $projectInfo = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_UPDATE_PROJECT_INFO,
            $requestBag
        );

        return $this->json($projectInfo, $projectInfo['isError'] ? 400 : 200);
    }
}
