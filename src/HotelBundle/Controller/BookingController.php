<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Booking;
use HotelBundle\Entity\User;
use HotelBundle\Form\BookingType;
use HotelBundle\Service\Bookings\BookingServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends Controller
{
    /**
     * @var BookingServiceInterface
     */
    private $bookingService;
    
    /**
     * BookingController constructor.
     * @param BookingServiceInterface $bookingService
     */
    public function __construct(
        BookingServiceInterface $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    
    /**
     * @Route("/createbooking", name="booking_create", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('bookings/create.html.twig',
            ['form' => $this
                ->createForm(BookingType::class)
                ->createView()]);
    }

    /**
     * @Route("/createbooking", methods={"POST"})
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
        return $this->redirectToRoute("hotel_index");
    }


    /**
     * @Route("/booking/{id}", name="booking_view")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) 
    {
        $booking = $this->bookingService->getOne($id);

        if (null === $booking){
            return $this->redirectToRoute("blog_index");
        }

        return $this->render("bookings/view.html.twig",
            ['booking' => $booking ]);

    }

    /**
     * @param Booking $booking
     * @return bool
     */
    private function isUserOrAdmin(Booking $booking)
    {   /** @var User $currentUser */
        $currentUser = $this->getUser();

        if(!$currentUser->isUser($booking) && !$currentUser->isAdmin()) {
            return false;
        }
        return true;
    }

    /**
     * @Route("/users/my_bookings", name="my_bookings")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllBookingsByUser()
    {
        $bookings = $this->bookingService->getAllBookingByUser();

        return $this->render("users/myBookings.html.twig",
            [
                'bookings' => $bookings
            ]);
    }

}