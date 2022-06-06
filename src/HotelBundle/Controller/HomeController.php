<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Category;
use HotelBundle\Entity\Role;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use HotelBundle\Entity\Roles;
use HotelBundle\Service\Roles\RoleServiceInterface;
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
     * @var RoleServiceInterface
     */
    private $roleService;
    
     /**
     * CategoryController constructor.
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(
        CategoryServiceInterface $categoryService,
        RoleServiceInterface $roleService)
    {
        $this->categoryService = $categoryService;
        $this->roleService = $roleService;
    }
    
    /**
     * @Route("/", name="hotel_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $rolesCount = $this->roleService->getCount();
    
        if ($rolesCount == 0) {
            $role = new Role();
            $role->setName('ROLE_USER');
            $role = $this->roleService->create($role);
            
            $role = new Role();
            $role->setName('ROLE_ADMIN');
            $role = $this->roleService->create($role);
        }
        
        $categories = $this->categoryService->getAll();
            
        return $this->render('home/index.html.twig',
        ['categories' => $categories]);
    }
}
