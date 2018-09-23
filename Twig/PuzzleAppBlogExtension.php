<?php
namespace Puzzle\App\BlogBundle\Twig;

use GuzzleHttp\Exception\BadResponseException;
use Puzzle\ConnectBundle\ApiEvents;
use Puzzle\ConnectBundle\Event\ApiResponseEvent;
use Puzzle\ConnectBundle\Service\PuzzleAPIClient;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class PuzzleAppBlogExtension extends \Twig_Extension
{
    /**
     * @var PuzzleAPIClient $apiClient
     */
    protected $apiClient;
    
    /**
	 * @var RequestStack $requestStack
	 */
	protected $requestStack;
	
	/**
	 * @var EventDispatcherInterface $dispatcher
	 */
	protected $dispatcher;
    
    public function __construct(RequestStack $requestStack, EventDispatcherInterface $dispatcher, PuzzleAPIClient $apiClient) {
        $this->requestStack = $requestStack;
        $this->apiClient = $apiClient;
        $this->dispatcher = $dispatcher;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('render_blog_articles', [$this, 'getArticles'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_blog_article', [$this, 'getArticle'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_blog_categories', [$this, 'getCategories'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_blog_category', [$this, 'getCategory'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getArticles($filter = null, $limit = null, $order = null, $page = null) {
        try {
            $query = [
                'filter' => $filter,
                'limit' => $limit,
                'orderBy' => $order,
                'page' => $page
            ];
            $articles = $this->apiClient->pull('/blog/articles', $query);
        }catch (BadResponseException $e) {
            $this->dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $this->requestStack->getCurrentRequest()));
            $articles = [];
        }
        
        return $articles;
    }
    
    public function getArticle($id) {
        try {
            $article = $this->apiClient->pull('/blog/articles/'.$id);
        }catch (BadResponseException $e) {
            $this->dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $this->requestStack->getCurrentRequest()));
            $article = [];
        }
        
        return $article;
    }
    
    public function getCategories($filter = null, $limit = null, $order = null, $page = null) {
        try {
            $query = [
                'filter' => $filter,
                'limit' => $limit,
                'orderBy' => $order,
                'page' => $page
            ];
            $categories = $this->apiClient->pull('/blog/categories', $query);
        }catch (BadResponseException $e) {
            $this->dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $this->requestStack->getCurrentRequest()));
            $categories = [];
        }
        
        return $categories;
    }
    
    public function getCategory($id) {
        try {
            $category = $this->apiClient->pull('/blog/categories/'.$id);
        }catch (BadResponseException $e) {
            $this->dispatcher->dispatch(ApiEvents::API_BAD_RESPONSE, new ApiResponseEvent($e, $this->requestStack->getCurrentRequest()));
            $category = [];
        }
        
        return $category;
    }
}
