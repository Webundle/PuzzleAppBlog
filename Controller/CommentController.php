<?php
namespace Puzzle\App\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Exception\BadResponseException;
use Puzzle\ConnectBundle\ApiEvents;
use Puzzle\ConnectBundle\Event\ApiResponseEvent;
use Puzzle\ConnectBundle\Service\PuzzleApiObjectManager;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class CommentController extends Controller
{
	/**
     * @var array $fields
     */
    private $fields;
    
    public function __construct() {
        $this->fields = ['parent', 'article', 'content'];
    }
    
	/***
	 * List comments
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function listAction(Request $request) {
		try {
		    /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
    		$apiClient = $this->get('puzzle_connect.api_client');
    		$article = null;
    		if ($articleId = $request->query->get('articleId')) {
    		    $article = $apiClient->pull('/blog/articles/'.$articleId);
    		    $comments = $article['_embedded']['comments'] ?? null;
    		}else {
    		    $comments = $apiClient->pull('/blog/comments');
    		}
		}catch (BadResponseException $e) {
		    /** @var EventDispatcher $dispatcher */
		    $dispatcher = $this->get('event_dispatcher');
		    $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
		    $comments = $article = [];
		}
		
		return $this->render($this->getParameter('app_blog_templates')['comment']['list'],[
		    'article' => $article,
		    'comments' => $comments
		]);
	}
	
	/***
	 * Create a new comment
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createAction(Request $request) {
	    $articleId = $request->query->get('article');
	    $parentId = $request->query->get('parent');
	    $data = PuzzleApiObjectManager::hydratate($this->fields, [
	        'parent' => $parentId,
	        'article' => $articleId
	    ]);
	    
	    if ($request->isMethod('POST') === true) {
	        $postData = $request->request->all();
	        $postData['authorName'] = $this->getUser()->getFullName();
	        $postData['authorEmail'] = $this->getUser()->getEmail();
	        $postData = PuzzleApiObjectManager::sanitize($postData);
	        
	        try {
	            /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
	            $apiClient = $this->get('puzzle_connect.api_client');
	            $apiClient->push('post', '/blog/comments', $postData);
	            
	            if ($request->isXmlHttpRequest() === true) {
	                return new JsonResponse(true);
	            }
	            
	            $this->addFlash('success', $this->get('translator')->trans('message.post', [], 'success'));
	            
	            return $this->redirectToRoute('app_blog_article', array('id' => $articleId));
	        }catch (BadResponseException $e) {
	            /** @var EventDispatcher $dispatcher */
	            $dispatcher = $this->get('event_dispatcher');
	            $event = $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
	            
	            if ($request->isXmlHttpRequest() === true) {
	                return $event->getResponse();
	            }
	            
	            return $this->redirectToRoute('app_blog_article', array('id' => $articleId));
	        }
	    }
	    
	    return $this->render($this->getParameter('app_blog_templates')['comment']['create']);
	}
	
	/***
	 * Show a comment
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showAction(Request $request, $id) {
	    try {
	        /** @var Puzzle\ConectBundle\Service\PuzzleAPIClient $apiClient */
	        $apiClient = $this->get('puzzle_connect.api_client');
	        $comment = $apiClient->pull('/blog/comments/'. $id);
	        
	        return $this->render($this->getParameter('app_blog_templates')['comment']['show'],[
	            'comment' => $comment
	        ]);
	    }catch (BadResponseException $e) {
	        /** @var EventDispatcher $dispatcher */
	        $dispatcher = $this->get('event_dispatcher');
	        $event  = $dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $request));
	        
	        return $event->getResponse();
	    }
	}
}
