<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Category;
use HotelBundle\Entity\Role;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use HotelBundle\Service\Rooms\RoomServiceInterface;
use HotelBundle\Entity\Roles;
use HotelBundle\Service\Roles\RoleServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;
    
    /**
     * @var RoomServiceInterface
     */
    private $roomService;
    
    /**
     * @var RoleServiceInterface
     */
    private $roleService;
    
     /**
     * CategoryController constructor.
     * @param CategoryServiceInterface $categoryService
     * @param RoomServiceInterface $roomService
     */
    public function __construct(
        CategoryServiceInterface $categoryService,
        RoomServiceInterface $roomService,
        RoleServiceInterface $roleService)
    {
        $this->categoryService = $categoryService;
        $this->roomService = $roomService;
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
    
    /**
     * @Route("/search-room", name="search_room", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchRoom()
    {
        $categories = $this->categoryService->getAll();
            
        return $this->render('home/index.html.twig',
        ['categories' => $categories]); 
    }
    
    /**
     * @Route("/search-room", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function findRoom(Request $request)
    {
        $checkin = ''; $checkout = ''; $roomsResult = '';
        
        $checkinSearch = $request->get('checkin');
        $checkoutSearch = $request->get('checkout');
        
        if ( ($checkinSearch != null) && ($checkoutSearch != null) )  {
            $checkin = date("Y-m-d", strtotime($checkinSearch)); 
            $checkout = date("Y-m-d", strtotime($checkoutSearch));
            
            $roomsResult = $this->roomService->findAllByCheckinCheckout($checkin, $checkout);
            
            if (!$roomsResult) {  
                $this->addFlash("warning", "Not found rooms with checkin = $checkin, checkout = $checkout. Please, try again!");
            } else {    
                $this->addFlash("info", "Result rooms with checkin = $checkin, checkout = $checkout");
            } 
               
        } else {
            $this->addFlash("errors", "All fields are required. Please select!");
        }
            
        return $this->render("home/findRoomResult.html.twig",
            [
                'roomsResult' => $roomsResult,
                'checkin' => $checkin,
                'checkout' => $checkout,
            ]
        );
        
    }
    
}
