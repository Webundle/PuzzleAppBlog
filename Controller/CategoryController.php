<?php
namespace Puzzle\App\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\ConnectBundle\ApiEvents;
use GuzzleHttp\Exception\BadResponseException;
use Puzzle\ConnectBundle\Event\ApiResponseEvent;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class CategoryController extends Controller
{
	/***
	 * List categories
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function listAction(Request $request, $current = "NULL"){
        try {
            $criteria = [];
            $criteria['filter'] = 'parent=='.$current;
            
            /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
            $apiClient = $this->get('puzzle_connect.api_client');
            $categories = $apiClient->pull('/blog/categories', $criteria);
            $currentCategory = $current != "NULL" ? $apiClient->pull('/blog/categories/'.$current) : null;
            
            if ($currentCategory && isset($currentCategory['_embedded']) && isset($currentCategory['_embedded']['parent'])) {
                $parent = $currentCategory['_embedded']['parent'];
            }else {
                $parent = null;
            }
            
        }catch (BadResponseException $e) {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
            $categories = $currentCategory = $parent = [];
        }
		
        return $this->render($this->getParameter('app_blog_templates')['category']['list'],[
		    'categories'      => $categories,
		    'currentCategory' => $currentCategory,
		    'parent'          => $parent
		]);
	}

    /***
     * Show category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $id) {
        try {
            /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
            $apiClient = $this->get('puzzle_connect.api_client');
            $category = $apiClient->pull('/blog/categories/'.$id);
            
            if (isset($category['files']) && count($category['files']) > 0){
                $criteria = [];
                $criteria['filter'] = 'id=:'.implode(';', $category['files']);
                
                $articles = $apiClient->pull('/blog/artilces', $criteria);
            }else {
                $articles = null;
            }
        }catch (BadResponseException $e) {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
            $category = $articles = [];
        }
        
        return $this->render($this->getParameter('app_blog_templates')['category']['list'], array(
            'currentCategory' => $category,
            'childs' => $category['_embedded']['childs'] ?? null,
            'articles' => $articles,
            'parent' => $category['_embedded']['parent'] ?? null,
        ));
    }
}
