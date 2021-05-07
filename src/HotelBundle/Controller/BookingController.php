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
     * @Route("/create-booking", name="booking_create", methods={"GET"})
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
     * @Route("/create-booking", methods={"POST"})
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
     * @Route("/users/my-bookings", name="my_bookings")
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

}