<?php
namespace Puzzle\App\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Exception\BadResponseException;
use Puzzle\ConnectBundle\ApiEvents;
use Puzzle\ConnectBundle\Event\ApiResponseEvent;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class ArticleController extends Controller
{
	/***
	 * List articles
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function listAction(Request $request) {
		try {
		   /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
		  $apiClient = $this->get('puzzle_connect.api_client');
		  $articles = $apiClient->pull('/blog/articles');
		}catch (BadResponseException $e) {
		    /** @var EventDispatcher $dispatcher */
		    $dispatcher = $this->get('event_dispatcher');
		    $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
		    $articles = [];
		}
		
		return $this->render($this->getParameter('app_blog_templates')['article']['list'],['articles' => $articles]);
	}
	
    /***
     * Show article
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $id) {
        try {
            /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
            $apiClient = $this->get('puzzle_connect.api_client');
            $article = $apiClient->pull('/blog/articles/'.$id);
            $category = $article['_embedded']['category'];
        }catch (BadResponseException $e) {
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
            $article = $category = [];
        }
        
        return $this->render($this->getParameter('app_blog_templates')['article']['show'], array(
            'article' => $article,
            'category' => $category
        ));
    }
}
