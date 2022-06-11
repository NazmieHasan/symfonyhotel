<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\User;
use HotelBundle\Form\UserType;
use HotelBundle\Entity\Booking;
use HotelBundle\Form\BookingType;
use HotelBundle\Service\Users\UserServiceInterface;
use HotelBundle\Service\Roles\RoleServiceInterface;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use HotelBundle\Service\Payments\PaymentServiceInterface;
use HotelBundle\Service\Bookings\BookingServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;
    
    /**
     * @var RoleServiceInterface
     */
    private $roleService;
    
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;
    
    /**
     * @var PaymentServiceInterface
     */
    private $paymentService;
    
    /**
     * @var BookingServiceInterface
     */
    private $bookingService;

    public function __construct(
        UserServiceInterface $userService,
        RoleServiceInterface $roleService,
        CategoryServiceInterface $categoryService,
        PaymentServiceInterface $paymentService,
        BookingServiceInterface $bookingService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->categoryService = $categoryService;
        $this->paymentService = $paymentService;
        $this->bookingService = $bookingService;
    }
    
    /**
     * @Route("/register", name="user_register", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        return $this->render('users/register.html.twig', [
            'form' => $this->createForm(UserType::class)->createView()
        ]);
    }

    /**
     * @Route("/register", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerProcess(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $requestEmail = $form['email']->getData();
        
        $usersCount = $this->userService->getCount();
        
        if ($requestEmail !== null) {

            if (null !== $this->userService->findOneByEmail($requestEmail)) {
                $email = $this->userService->findOneByEmail($requestEmail)->getEmail();

                $this->addFlash("errors", "Email  $email already taken!");
                
                return $this->returnRegisterView($user);
            }
        }

        if ($form->isValid()) {
        
            if ($usersCount == 0) {
                $userRole = $this->roleService->findOneBy("ROLE_ADMIN");
                $user->addRole($userRole);
                $this->addFlash("info", "You are registered first! You have an admin role. Login in system.");
            } else {
                $this->addFlash("info", "You are registered! Login in system.");
            }
            
            $this->userService->save($user);
            return $this->redirectToRoute("security_login");
        }

        return $this->render('users/register.html.twig',
            [
                'form' => $form->createView()
            ]);

    }
    
    /**
     * @Route("/user/create-booking/category-{id}", name="user_booking_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, int $id)
    {
        $category = $this->categoryService->getOne($id);
        $payments = $this->paymentService->getAll();
        
        return $this->render('users/createBooking.html.twig',
            [
                'form' => $this->createForm(BookingType::class)->createView(),
                'category' => $category,
                'payments' => $payments,
            ]);
    }

    /**
     * @Route("/user/create-booking/category-{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request, int $id)
    {
        $booking = new Booking();
        $category = $this->categoryService->getOne($id);
        $payments = $this->paymentService->getAll();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $this->bookingService->create($booking, $id);
            $this->addFlash("info", "Create booking successfully!");
            return $this->redirectToRoute("my_bookings");
        }
        
        return $this->render('users/createBooking.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
                'payments' => $payments,
            ]);
    }
    
    /**
     * @Route("/user/my-bookings", name="my_bookings")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllMyBookings()
    {
        $bookings = $this->bookingService->getAllByCurrentUser();

        return $this->render("users/myBookings.html.twig",
            [
                'bookings' => $bookings
            ]);
    }
    
    /**
     * @Route("/logout", name="security_logout")
     * @throws \Exception
     */
    public function logout() {
        throw new \Exception("Logout failed!");
    }
    
    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function returnRegisterView(User $user): Response
    {
        return $this->render("users/register.html.twig",
            [
                'user' => $user,
                'form' => $this->createForm(UserType::class)
                    ->createView()
            ]);
    }

}
