<?php

namespace App\Controller;

use App\Security\WebserviceUser;
use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        return $this->redirectToRoute('editProjectBasic');

//        return $this->render(
//            'Project/Create/chooseType.html.twig',
//            [
//                'user' => $this->getUser()
//            ]
//        );
    }


    /**
     * @param ApiService $apiService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Exception
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
            throw new \Exception($response);
        }

        //ROUTE_PE_GET_CATEGORIES
        $category_response = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_GET_CATEGORIES,
            [],
            ApiService::METHOD_GET
        );
        $county_array = [
            'TOATA TARA',
            'BUCURESTI',
            'ALBA',
            'ARAD',
            'ARGES',
            'BACAU',
            'BIHOR',
            'BISTRITA-NASAUD',
            'BOTOSANI',
            'BRAILA',
            'BRASOV',
            'BUZAU',
            'CALARASI',
            'CARAS-SEVERIN',
            'CLUJ',
            'CONSTANTA',
            'COVASNA',
            'DAMBOVITA',
            'DOLJ',
            'GALATI',
            'GIURGIU',
            'HARGHITA'
            ,
            'HUNEDOARA',
            'IALOMITA',
            'IASI',
            'ILFOV',
            'MARAMURES',
            'MEHEDINTI',
            'MURES',
            'NEAMT',
            'OLT',
            'PRAHOVA',
            'SALAJ',
            'SATU-MARE',
            'SIBIU',
            'SUCEAVA',
            'TELEORMAN',
            'TIMIS',
            'TULCEA',
            'VALCEA',
            'VASLUI',
            'VRANCEA'
        ];

        return $this->render(
            'Project/Create/createProjectBasic.html.twig',
            [
                'user' => $user,
                'projectData' => $response['data'],
                'category_data' => $category_response,
                'county_array' => $county_array,
                'now' => (new \DateTime())->format('Y-m-d'),
                'maxDate' => (new \DateTime())->add(new \DateInterval('P3M'))->format('Y-m-d')
            ]
        );

    }


    public function saveProjectBasic(Request $request, string $link, int $projectId, ApiService $apiService)
    {
        $arrayToSend = [
            'link' => $link,
            'project_id' => $projectId,
            'short_description' => $request->request->get('comment'),
            'title' => $request->request->get('project-title'),
            'tags' => $request->request->get('tags'),
            'expiration_date' => $request->request->get('expiration-date'),
            'total_amount' => $request->request->get('total-amount'),
            'category_id' => $request->request->get('category')
        ];

        $response = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_UPDATE_PROJECT_INFO,
            $arrayToSend
        );

        return $this->redirectToRoute(
            'editProject',
            [
                'projectLink' => $link,
                'projectId' => $projectId
            ]
        );
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
            throw new \Exception($projectInfo);
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
        $perkId = $request->request->get('perkId');

        $perkInfo = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_REMOVE_PERK,
            [
                'project_id' => $projectId,
                'perk_id' => $perkId
            ]
        );

        return new JsonResponse($perkInfo);
    }

    /**
     * @param Request $request
     * @param ApiService $apiService
     * @return JsonResponse
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function addPerk(Request $request, ApiService $apiService)
    {
        $projectId = $request->request->get('projectId');
        $perkInfo = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_ADD_PERK,
            [
                'project_id' => $projectId,
                'title' => 'Click aici pentru a edita titlul...',
                'amount' => random_int(0, 100),
                'description' => 'Descrierea recompensei...'
            ]
        );
        
        $response = '<div id="perk-' . $perkInfo['data']['id'] . '" class="gallery-cell">
                <div class="card" style="width: 18rem; left: 10px;">
                    <span id="remove-perk-' . $perkInfo['data']['id'] . '" class="close sticky-top" data-effect="fadeOut"
                          onclick="deletePerk(' . $perkInfo['data']['id'] . ')" style="position: absolute; margin-left: 16.7rem"><i
                                class="fa fa-times"></i></span>
                    <div id="perk-image-' . $perkInfo['data']['id'] . '" class="perk-image" data-perk-id="' . $perkInfo['data']['id'] . '">
                        <div id="keditor-content-area-' . $perkInfo['data']['id'] . '" class="keditor-content-area ui-sortable">
                            <section class="keditor-ui keditor-container keditor-initialized-container"
                                     id="keditor-container-' . $perkInfo['data']['id'] . '">
                                <section class="keditor-ui keditor-container-inner">
                                    <div class="row">
                                        <div class="col-md-12 keditor-container-content ui-sortable"
                                             data-type="container-content"
                                             id="keditor-container-content-' . $perkInfo['data']['id'] . '">
                                            <section class="keditor-ui keditor-component keditor-initialized-component"
                                                     data-type="component-photo"
                                                     id="keditor-component-' . $perkInfo['data']['id'] . '">
                                                <section class="keditor-ui keditor-component-content"
                                                         id="keditor-component-content-' . $perkInfo['data']['id'] . '">
                                                    <div class="photo-panel">
                                                        <img class="card-img-top"
                                                             src="' . $perkInfo['data']['image_path'] . '"
                                                             width="100%" height="" style="display: inline-block;">
                                                    </div>
                                                </section>
                                                <div class="keditor-toolbar keditor-toolbar-component">
                                                    <a href="javascript:void(0);"
                                                       class="keditor-ui btn-component-setting">
                                                        <i class="fa fa-cog"></i>
                                                    </a>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </section>
                            </section>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title perk-title ckeditor-enabled-' . $perkInfo['data']['id'] . '" data-perk-id="' . $perkInfo['data']['id'] . '"
                            id="perk-title-' . $perkInfo['data']['id'] . '"
                            contenteditable="true">' . $perkInfo['data']['title'] . '</h5>
                        <p class="card-text perk-description ckeditor-enabled-' . $perkInfo['data']['id'] . '" data-perk-id="' . $perkInfo['data']['id'] . '"
                           id="perk-description-' . $perkInfo['data']['id'] . '"
                           contenteditable="true" style="max-height: 160px; overflow-x: auto">' . $perkInfo['data']['description'] . '</p>
                    </div>
                    <div class="card-footer">
                        <a class="btn-perk-amount btn-primary rounded text-white offset-3 col-6 d-block mx-auto">
                            <div class="quantity">
                                <input type="number" min="1" step="1" data-perk-id="' . $perkInfo['data']['id'] . '"
                                       value="' . $perkInfo['data']['amount'] . '" class="btn-primary perk-amount">
                                RON
                            </div>
                        </a>
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
        $savedData = json_decode($request->getContent(), true);
        $projectId = $request->get('projectId');

        $title = strip_tags($savedData['title']);
        $shortDescription = $savedData['shortDescription'];
        $content = $savedData['content'];
        $totalAmount = $savedData['totalAmount'];

        $media = str_replace(array("\n", "\r"), '', $savedData['presentationMedia']);
        preg_match('/(?<=src=")[1a-zA-Z:\/.0-9]+(?=">)/', $media, $matches);
        $presentationMedia = $matches[0];

        $perks = $savedData['perks'];

        foreach ($perks as $id => &$perk) {
            $perkImage = $perk['image_path'];
            preg_match('/(?<=src=")[1a-zA-Z:\/_.0-9]+/', $perkImage, $matches);
            $perk['image_path'] = $matches[0];
            $perk['perk_id'] = $id;
        }

        $requestBag = [
            'project_id' => $projectId,
            'title' => $title,
            'short_description' => $shortDescription,
            'content' => $content,
            'presentation_media' => $presentationMedia,
            'total_amount' => $totalAmount
        ];

        $projectInfo = $apiService->callProjectsEngineApi(
            ApiService::ROUTE_PE_UPDATE_PROJECT_INFO,
            $requestBag
        );


        $perksInfo = null;
        if (count($perks) > 0) {
            $requestBag = [
                'data' => $perks
            ];

            $perksInfo = $apiService->callProjectsEngineApi(
                ApiService::ROUTE_PE_UPDATE_PERK_INFO,
                $requestBag
            );
        }

        return $this->json($projectInfo, $projectInfo['isError'] || (null !== $perksInfo && $perksInfo['isError']) ? 400 : 200);
    }

    public function saveImage(Request $request)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('image');
        $uniqueId = uniqid('image_', true);

        $finalImage = copy($file->getPathname(), '/var/www/static/images/' . $uniqueId);
        $imagesHost = 'http://' . $this->getParameter('images_host') . '/';

        return new JsonResponse(['link' => $imagesHost . $uniqueId]);
    }
}
