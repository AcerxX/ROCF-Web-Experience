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

        //ROUTE_PE_GET_CATEGORIES
        $category_response = $apiService->callProjectsEngineApi(
          ApiService::ROUTE_PE_GET_CATEGORIES,
          [],
            ApiService::METHOD_GET
        );
        $county_array = ['TOATA TARA', 'BUCURESTI', 'ALBA', 'ARAD', 'ARGES', 'BACAU', 'BIHOR', 'BISTRITA-NASAUD', 'BOTOSANI', 'BRAILA', 'BRASOV', 'BUZAU', 'CALARASI', 'CARAS-SEVERIN', 'CLUJ', 'CONSTANTA', 'COVASNA', 'DAMBOVITA', 'DOLJ', 'GALATI', 'GIURGIU', 'HARGHITA'
            , 'HUNEDOARA', 'IALOMITA', 'IASI', 'ILFOV', 'MARAMURES', 'MEHEDINTI', 'MURES', 'NEAMT', 'OLT', 'PRAHOVA', 'SALAJ', 'SATU-MARE', 'SIBIU', 'SUCEAVA', 'TELEORMAN', 'TIMIS', 'TULCEA', 'VALCEA', 'VASLUI', 'VRANCEA'
        ];
        return $this->render(
            'Project/Create/createProjectBasic.html.twig',
            [
                'user' => $user,
                'projectData' => $response['data'],
                'category_data'=>$category_response,
                'county_array' => $county_array
            ]
        );

    }


    public function saveProjectBasic(Request $request, string $link,int $projectId, ApiService $apiService)
    {

        $formData = $request->request->all();
        $arrayToSend = [
            "link"=>$link,
            "project_id"=>$projectId,
            "short_description"=>$formData['comment'],
            "title"=>$formData['project-title'],
            "presentation_media"=>$formData['tags'],
            "expiration_date"=>$formData['exp_date']
        ];
        $response = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_UPDATE_PROJECT_INFO,
            $arrayToSend
        );
        /**
         * TODO API CALL UPDATE PROJECT API WITH INPUT DATA
         */
        //api call ROUTE_PE_UPDATE_PROJECT_INFO si in array trimit project_id
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


        if ($projectInfo['isError'] || $projectInfo['data']['userId'] !== $loggedUser->getId()) {
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
}
