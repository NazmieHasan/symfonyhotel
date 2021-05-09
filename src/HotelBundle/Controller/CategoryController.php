<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Category;
use HotelBundle\Form\CategoryType;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller
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
     * @Route("/view/{id}", name="category_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) {
        $category = $this->categoryService->getOne($id);

        return $this->render("categories/view.html.twig",
            ['category' => $category ]);
    }

}
