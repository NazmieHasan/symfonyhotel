<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\User;
use HotelBundle\Form\UserType;
use HotelBundle\Entity\Booking;
use HotelBundle\Form\BookingType;
use HotelBundle\Service\Users\UserServiceInterface;
use HotelBundle\Service\Roles\RoleServiceInterface;
use HotelBundle\Service\Rooms\RoomServiceInterface;
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
     * @var RoomServiceInterface
     */
    private $roomService;
    
    
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
        RoomServiceInterface $roomService,
        CategoryServiceInterface $categoryService,
        PaymentServiceInterface $paymentService,
        BookingServiceInterface $bookingService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->roomService = $roomService;
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
     * @Route("/user/create-booking/category-{catId}", name="user_booking_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $catId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, int $catId)
    {
        $category = $this->categoryService->getOne($catId);
        $payments = $this->paymentService->getAll();
        
        return $this->render('users/createBooking.html.twig',
            [
                'form' => $this->createForm(BookingType::class)->createView(),
                'category' => $category,
                'payments' => $payments,
            ]);
    }

    /**
     * @Route("/user/create-booking/category-{catId}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $catId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request, int $catId)
    {
        $booking = new Booking();
        $category = $this->categoryService->getOne($catId);
        
        $payments = $this->paymentService->getAll();
        
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        
        $checkin = $form['checkin']->getData();
        $checkout = $form['checkout']->getData();
        
        $roomRes = $this->roomService->findOneByCheckinCheckoutCategory($checkin, $checkout, $catId);
        
        if (!$roomRes) {
            $this->addFlash("warning", "Not found room! Please, try again!");
            return $this->redirectToRoute('user_booking_create', [ 'catId' => $catId]);
        } 
        
        $rId = $roomRes[0]->getId();
        $room = $this->roomService->getOne($rId);
        
        if ($form->isValid()) {
            $this->bookingService->create($booking, $catId, $rId);
            $this->addFlash("info", "Create booking successfully!");
            return $this->redirectToRoute("my_bookings");
        }
        
        return $this->render('users/createBooking.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
                'room' => $room,
                'payments' => $payments,
            ]);
    }
    
    /**
     * @Route("/user/create-booking/category-{catId}/room-{rId}/checkin={checkin}/checkout={checkout}", name="user_booking_create_with_search_form", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $catId
     * @param $rId
     * @param $checkin
     * @param $checkout
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createWithSearchForm(Request $request, int $catId, int $rId, string $checkin, string $checkout)
    {
        $category = $this->categoryService->getOne($catId);
        $room = $this->roomService->getOne($rId);
        $payments = $this->paymentService->getAll();
        
        $form = $this->createForm(BookingType::class);
        
        return $this->render('users/createBookingWithSearchForm.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
                'room' => $room,
                'payments' => $payments,
                'checkin' => $checkin,
                'checkout' => $checkout,
            ]);
    }

    /**
     * @Route("/user/create-booking/category-{catId}/room-{rId}/checkin={checkin}/checkout={checkout}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $catId
     * @param $rId
     * @param $checkin
     * @param $checkout
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createWithSearchFormProcess(Request $request, int $catId, int $rId, string $checkin, string $checkout)
    {
        $booking = new Booking();
        $category = $this->categoryService->getOne($catId);
        $room = $this->roomService->getOne($rId);
        $payments = $this->paymentService->getAll();
        
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        
        $checkinDateFormat = new \DateTime($checkin);
        $booking->setCheckin($checkinDateFormat);
        
        $checkoutDateFormat = new \DateTime($checkout);
        $booking->setCheckout($checkoutDateFormat);
        
        if ($form->isValid()) {
            $this->bookingService->create($booking, $catId, $rId);
            $this->addFlash("info", "Create booking successfully!");
            return $this->redirectToRoute("my_bookings");
        }
        
        return $this->render('users/createBookingWithSearchForm.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
                'room' => $room,
                'payments' => $payments,
                'checkin' => $checkin,
                'checkout' => $checkout,
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
