<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Category;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;
    
     /**
     * CategoryController constructor.
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(
        CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    
    /**
     * @Route("/", name="hotel_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $categories = $this->categoryService->getAll();
            
        return $this->render('home/index.html.twig',
        ['categories' => $categories]);
    }
}
