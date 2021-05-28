<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Booking;
use HotelBundle\Entity\Stay;
use HotelBundle\Entity\Room;
use HotelBundle\Entity\Guest;
use HotelBundle\Entity\User;
use HotelBundle\Form\BookingType;
use HotelBundle\Form\StayType;
use HotelBundle\Form\RoomType;
use HotelBundle\Form\GuestType;
use HotelBundle\Service\Bookings\BookingServiceInterface;
use HotelBundle\Service\Stays\StayServiceInterface;
use HotelBundle\Service\Rooms\RoomServiceInterface;
use HotelBundle\Service\Guests\GuestServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/bookings")
 * Class BookingController
 * @package HotelBundle\Controller\Admin
 */
class BookingController extends Controller
{
    /**
     * @var BookingServiceInterface
     */
    private $bookingService;
    
    /**
     * @var StayServiceInterface
     */
    private $stayService;
    
    /**
     * @var RoomServiceInterface
     */
    private $roomService;
    
    /**
     * @var GuestServiceInterface
     */
    private $guestService;
    
    /**
     * BookingController constructor.
     * @param BookingServiceInterface $bookingService
     * @param StayServiceInterface $stayService
     * @param RoomServiceInterface $roomService
     * @param GuestServiceInterface $guestService
     */
    public function __construct(
        BookingServiceInterface $bookingService,
        StayServiceInterface $stayService,
        RoomServiceInterface $roomService,
        GuestServiceInterface $guestService)
    {
        $this->bookingService = $bookingService;
        $this->stayService = $stayService;
        $this->roomService = $roomService;
        $this->guestService = $guestService;
    }
    
    /**
     * @Route("/create", name="admin_booking_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('admin/bookings/create.html.twig',
            ['form' => $this
                ->createForm(BookingType::class)
                ->createView()]);
    }

    /**
     * @Route("/create", methods={"POST"})
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
        return $this->redirectToRoute("admin_bookings");
    }
    
    /**
     * @Route("/view/{id}", name="admin_booking_view")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) {
        $booking = $this->bookingService->getOne($id);
        $rooms = $this->roomService->getAll();
        $guests = $this->guestService->getAll();
        
        $form = $this->createForm(StayType::class);
        
        $stays = $this->stayService->getAllByBookingId($id);

        return $this->render("admin/bookings/view.html.twig",
            [
                'form' => $this
                ->createForm(StayType::class)
                ->createView(),
                'booking' => $booking,
                'rooms' => $rooms,
                'guests' => $guests,
                'stays' => $stays,
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_booking_edit", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $booking = $this->bookingService->getOne($id);
        
        if (null === $booking){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/bookings/edit.html.twig',
            [
                'form' => $this->createForm(BookingType::class)
                       ->createView(),
                'booking' => $booking
            ]);

    }

    /**
     * @Route("/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Responsen
     */
    public function editProcess(Request $request, int $id)
    {
        $booking = $this->bookingService->getOne($id);

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        $this->bookingService->edit($booking);

        return $this->redirectToRoute("admin_bookings");
    }

    /**
     * @Route("/delete/{id}", name="admin_booking_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $booking = $this->bookingService->getOne($id);
        
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        $this->bookingService->delete($booking);
        $this->addFlash("info", "Booking is deleted");
        
        return $this->redirectToRoute("admin_bookings");
    }

    /**
     * @Route("/", name="admin_bookings")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllBookings(Request $request)
    {
        $bookings = $this->bookingService->getAll();
        
        if($request->isMethod("POST")) {  
            $statusId = $request->get('statusId');
        
            $em = $this->getDoctrine()->getManager();
            $bookings = $em->getRepository("HotelBundle:Booking")
                       ->findBy(
                           [
                               'statusId' => $statusId
                           ]);
        }

        return $this->render("admin/bookings/list.html.twig",
            [
                'bookings' => $bookings
            ]);
    }
}