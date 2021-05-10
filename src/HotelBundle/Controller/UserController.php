<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\User;
use HotelBundle\Form\UserType;
use HotelBundle\Entity\Booking;
use HotelBundle\Form\BookingType;
use HotelBundle\Service\Users\UserServiceInterface;
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
     * @var BookingServiceInterface
     */
    private $bookingService;

    public function __construct(
        UserServiceInterface $userService,
        BookingServiceInterface $bookingService)
    {
        $this->userService = $userService;
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

        if(null !== $this
                ->userService->findOneByEmail($form['email']->getData())) {
            $email = $this
                ->userService->findOneByEmail($form['email']->getData())
                ->getEmail();

            $this->addFlash("errors", "Email  $email already taken!");
            return $this->returnRegisterView($user);
        }

        if ($form->isValid()) {
            $this->addFlash("info", "You are registered successfully! Login in system.");
            $this->userService->save($user);
            return $this->redirectToRoute("security_login");
        }

        return $this->render('users/register.html.twig',
            [
                'form' => $form->createView()
            ]);

    }
    
    /**
     * @Route("user/create-booking", name="user_booking_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('users/createBooking.html.twig',
            ['form' => $this
                ->createForm(BookingType::class)
                ->createView()]);
    }

    /**
     * @Route("user/create-booking", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        
        $this->bookingService->create($booking);
        $this->addFlash("info", "Create booking successfully!");
        return $this->redirectToRoute("my_bookings");
    }
    
    /**
     * @Route("/user/my-bookings", name="my_bookings")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllBookingsByUser()
    {
        $bookings = $this->bookingService->getAllBookingsByUser();

        return $this->render("users/myBookings.html.twig",
            [
                'bookings' => $bookings
            ]);
    }
    
    /**
     * @Route("/logout", name="security_logout")
     * @throws \Exception
     */
    public function logout(){
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
