<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Role;
use HotelBundle\Entity\User;
use HotelBundle\Form\UserType;
use HotelBundle\Service\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/users")
 * Class UserController
 * @package HotelBundle\Controller\Admin
 */
class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;
    
    /**
     * UserController constructor.
     * @param UserServiceInterface $studentService
     */
    public function __construct(
        UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/", name="admin_users")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUsers(){
        $users = $this->userService->getAll();
        return $this->render('admin/users/list.html.twig', ['users' => $users]);

    }

}