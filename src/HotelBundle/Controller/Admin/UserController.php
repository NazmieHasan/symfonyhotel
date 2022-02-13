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
     * @Route("/", name="admin_users")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUsers() {
        $users = $this->userService->getAll();
        
        return $this->render('admin/users/list.html.twig', ['users' => $users]);

    }
    
    /**
     * @Route("/edit/{id}", name="admin_user_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editUser(Request $request, int $id) {
    
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        
        if ($user === null) {
            return $this->redirectToRoute("admin_users");
        }
        
        $originalPassword = $user->getPassword();
        
        $form = $this->createForm(UserEditType::class, $user);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $rolesRequest = $user->getRoles();
            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            $roles = [];
            
            foreach ($rolesRequest as $roleName) {
                $roles[] = $roleRepository->findOneBy(['name' => $roleName]);
            }
            
            $user->setRoles($roles);
            
            if ($user->getPassword()) {
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPassword());
                    $user->setPassword($password);
            } else {
                $user->setPassword($originalPassword);
            }
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute("admin_users");
        }
        
        return $this->render('admin/users/edit.html.twig', ['user' => $user,
            'form' => $form->createView()]);
    }
    
    /**
     * @Route("/delete/{id}", name="admin_user_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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