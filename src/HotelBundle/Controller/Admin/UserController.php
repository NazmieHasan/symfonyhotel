<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Role;
use HotelBundle\Entity\User;
use HotelBundle\Entity\Booking;
use HotelBundle\Form\UserType;
use HotelBundle\Form\UserEditType;
use HotelBundle\Service\Users\UserServiceInterface;
use HotelBundle\Service\Bookings\BookingServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @var BookingServiceInterface
     */
    private $bookingService;
    
    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param BookingServiceInterface $bookingService
     */
    public function __construct(
        UserServiceInterface $userService,
        BookingServiceInterface $bookingService)
    {
        $this->userService = $userService;
        $this->bookingService = $bookingService;
    }

    /**
     * @Route("/", name="admin_users", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllUsers()
    {
        $users = $this->userService->getAll();
        
        return $this->render("admin/users/list.html.twig",
            [
                'users' => $users
            ]);
    }
    
    /**
     * @Route("/",  methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFindUser(Request $request)
    {
        $user = '';
        $email = $request->get('email');
        $searchEmail = $this->userService->findOneByEmail($email);
        
        if ($searchEmail) {
            $id = $searchEmail->getId(); 
            $user = $this->userService->findOneById($id);
        }
        
        return $this->render("admin/users/listResult.html.twig",
            [
                'user' => $user
            ]
        );
    }
    
    /**
     * @Route("/view/{id}", name="admin_user_view")
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) 
    {
        $user = $this->userService->findOneById($id);
        
        $bookings = $this->bookingService->getAllByUserId($id);

        return $this->render("admin/users/view.html.twig",
            [
                'user' => $user,
                'bookings' => $bookings
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_user_edit",  methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(int $id) 
    {
        $user = $this->userService->findOneById($id);
        $form = $this->createForm(UserEditType::class, $user);
        
        if ($user === null) {
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/users/edit.html.twig', 
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
      
    /**
     * @Route("/edit/{id}", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editProcess(Request $request, int $id) 
    {
        $user = $this->userService->findOneById($id);
        
        $form = $this->createForm(UserEditType::class, $user);
        $form->remove('email');
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $rolesRequest = $user->getRoles();
            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            $roles = [];
            
            foreach ($rolesRequest as $roleName) {
                $roles[] = $roleRepository->findOneBy(['name' => $roleName]);
            }
            
            $user->setRoles($roles);
            
            $this->userService->update($user);
            return $this->redirectToRoute('admin_user_view', [ 'id' => $id]);
        }
        
        return $this->render('admin/users/edit.html.twig', 
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
    
    /**
     * @Route("/delete/{id}", name="admin_user_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $user = $this->userService->findOneById($id);
        
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        
        // if user has a booking, then not deleted
        $this->userService->delete($user);
        $this->addFlash("info", "User is deleted");
        
        return $this->redirectToRoute("admin_users");
    }

}
