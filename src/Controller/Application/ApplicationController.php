<?php


namespace App\Controller\Application;


use App\Entity\Application;
use App\Form\ApplicationType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends BaseController {
    /**
     * @Route("/application", name="application")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request){
        $key = $request->query->get('key');
        //$applicationSearch = $this->getDoctrine()->getRepository(Application::class)->findAllGreaterThanPrice(time() - 86400);
        $applicationSearch = $this->getDoctrine()->getRepository(Application::class)->findAllGreaterThanDate(time());

        if ($applicationSearch){
            //нашел за сегодня
            //var_dump($applicationSearch);
        }
        else{
            $result = $this->scrapApplication($key);
            foreach ($result as $item){
                $entityManager = $this->getDoctrine()->getManager();
                $applicationSearch = $entityManager->getRepository(Application::class)->findDuplicate($item['icon']);
                // занесение в бд если не существует
                if (empty($applicationSearch[0])){
                    $application = new Application();

                    $application->setStore($item['store']);
                    $application->setTitle($item['title']);
                    $application->setDescription($item['description']);
                    $application->setIcon($item['icon']);
                    $application->setScoreText($item['score']);
                    $application->setRatings($item['ratings']);
                    $application->setReviews($item['reviews']);
                    $application->setTop($item['top']);
                    $application->setDate(time());

                    $entityManager->persist($application);
                    $entityManager->flush();
                }
                // обновление
                else{
                    $application = $applicationSearch[0];
                    //текущий рейтинг
                    $application->setScoreText($item['score']);
                    //количество оценок
                    $application->setRatings($item['ratings']);
                    //количество отзывов
                    $application->setReviews($item['reviews']);
                    //текущая дата
                    $application->setDate(time());
                    //описание
                    $application->setDescription($application->getDescription());
                    //текущий рейтинг
                    $application->setTop($item['top']);

                    $entityManager->flush();
                }
            }
        }

        $forRender = parent::defaultRender();
        $forRender['application'] = $application;

        return $this->render('main/index.html.twig', $forRender);
    }

    /**
     * @param $key
     * @return array
     */
    public function scrapApplication($key){
        $patchStore = ['AppStore', 'GooglePlay'];
        $arrayApplication = [];
        $arrayKey = $topKey = 0;
        foreach ($patchStore as $store){
            $storeSrc = 'assets/main/js/'.$store.'/index.js';
            if (!file_exists($storeSrc))
                continue;

            $scrap = $this->getScrapInStore($storeSrc, $key);

            // Dell first and last word ('[' and ']')
            unset($scrap[0], $scrap[count($scrap) - 1]);
            foreach ($scrap as $keyScrap => $item){
                $trimmed = trim($item);

                // Search param
                if (
                    strpos($trimmed, 'title') !== 0 &&
                    strpos($trimmed, 'score:') !== 0 &&
                    strpos($trimmed, 'description:') !== 0 &&
                    strpos($trimmed, 'ratings') !== 0 &&
                    strpos($trimmed, 'reviews') !== 0 &&
                    strpos($trimmed, 'icon') !== 0
                ){
                    unset($scrap[$keyScrap]);
                    continue;
                }
                else{
                    // Add store and top
                    if (strpos($trimmed, 'title') === 0){
                        $arrayKey++;
                        $topKey++;
                        // Add store
                        $arrayApplication[$arrayKey]['store'] = $store;

                        // Add Top
                        //$arrayApplication[$arrayKey]['top'] = $arrayKey;
                        if ($arrayKey > 1){
                            $firstStore = $arrayApplication[$arrayKey - 1]['store'];
                            $secondStore = $arrayApplication[$arrayKey]['store'];
                            if($firstStore != $secondStore){
                                $topKey = 1;
                            }
                            $arrayApplication[$arrayKey]['top'] = $topKey;
                        }
                        else{
                            $arrayApplication[1]['top'] = $topKey;
                        }
                    }

                    // Add delimiter
                    if (strpos($trimmed, ": '") === false){
                        $trimmed = $this->replaceJSWord(': ', '/explode/', $trimmed,'1');
                    }
                    else{
                        $trimmed = $this->replaceJSWord(": '", '/explode/', $trimmed,'1');
                    }

                    // Explode
                    $trimmed = explode('/explode/', $trimmed);

                    // Add ratings in AppStore
                    if ($store == 'AppStore'){
                        if ($trimmed[0] == 'reviews'){
                            $arrayApplication[$arrayKey]['ratings'] = $trimmed[1];
                        }
                    }

                    // Dell bad word
                    $trimmed[1] = str_replace(['\r', '\n', "'", '+', '"'],'',$trimmed[1]);
                    $trimmed[1] = rtrim($trimmed[1], ',');
                    $trimmed[1] = trim($trimmed[1]);

                    if ($trimmed[0] == 'ratings'){
                        $trimmed[1] = str_replace(',','',$trimmed[1]);
                    }

                    $arrayApplication[$arrayKey][$trimmed[0]] = $trimmed[1];
                    unset($scrap[$keyScrap]);
                }
            }
        }
        return $arrayApplication;
    }

    /**
     * @param $search
     * @param $replace
     * @param $text
     * @param $c
     * @return false|string|string[]
     */
    public function replaceJSWord($search, $replace, $text, $c){
        if($c > substr_count($text, $search)){
            return false;
        }
        else{
            $arr = explode($search, $text);
            $result = '';
            $k = 1;
            foreach($arr as $value){
                $k == $c ? $result .= $value.$replace : $result .= $value.$search;
                $k++;
            }
            $pos = strripos($result,$search);
            $result = substr_replace($result,'', $pos, $pos + 3);
            return $result;
        }
    }

    /**
     * @param $patch
     * @param $key
     * @return mixed
     */
    public function getScrapInStore($patch, $key){
        exec("node ".$patch." ".$key." 2>&1", $out);
        return $out;
    }

    /**
     * @Route("/", name="application_search")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createFormApplication(Request $request){
        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $key = $application->getTitle();
            return $this->redirectToRoute('application', ['key' => $key]);
        }

        $forRender = parent::defaultRender();
        $forRender['main_title'] = $forRender['title'] = 'Поиск приложений';
        $forRender['form'] = $form->createView();

        return $this->render('main/search.html.twig', $forRender);
    }
}