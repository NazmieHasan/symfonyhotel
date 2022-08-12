<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Booking;
use HotelBundle\Entity\Stay;
use HotelBundle\Entity\Room;
use HotelBundle\Entity\Guest;
use HotelBundle\Entity\User;
use HotelBundle\Entity\Category;
use HotelBundle\Entity\Payment;
use HotelBundle\Entity\Status;
use HotelBundle\Form\BookingType;
use HotelBundle\Form\BookingEditType;
use HotelBundle\Form\StayType;
use HotelBundle\Form\RoomType;
use HotelBundle\Form\GuestType;
use HotelBundle\Form\CategoryType;
use HotelBundle\Form\PaymentType;
use HotelBundle\Form\StatusType;
use HotelBundle\Service\Bookings\BookingServiceInterface;
use HotelBundle\Service\Stays\StayServiceInterface;
use HotelBundle\Service\Rooms\RoomServiceInterface;
use HotelBundle\Service\Guests\GuestServiceInterface;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use HotelBundle\Service\Payments\PaymentServiceInterface;
use HotelBundle\Service\Statuses\StatusServiceInterface;
use Symfony\Component\Validator\Constraints\DateTime;
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
     * @var CategoryServiceInterface
     */
    private $categoryService;
    
    /**
     * @var PaymentServiceInterface
     */
    private $paymentService;
    
    /**
     * @var StatusServiceInterface
     */
    private $statusService;
    
    /**
     * BookingController constructor.
     * @param BookingServiceInterface $bookingService
     * @param StayServiceInterface $stayService
     * @param RoomServiceInterface $roomService
     * @param GuestServiceInterface $guestService
     * @param CategoryServiceInterface $categoryService
     * @param PaymentServiceInterface $paymentService
     * @param StatusServiceInterface $statusService
     */
    public function __construct(
        BookingServiceInterface $bookingService,
        StayServiceInterface $stayService,
        RoomServiceInterface $roomService,
        GuestServiceInterface $guestService,
        CategoryServiceInterface $categoryService,
        PaymentServiceInterface $paymentService,
        StatusServiceInterface $statusService)
    {
        $this->bookingService = $bookingService;
        $this->stayService = $stayService;
        $this->roomService = $roomService;
        $this->guestService = $guestService;
        $this->categoryService = $categoryService;
        $this->paymentService = $paymentService;
        $this->statusService = $statusService;
    }
    
    /**
     * @Route("/view/{id}", name="admin_booking_view", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) 
    {
        $booking = $this->bookingService->getOne($id);
        $form = $this->createForm(StayType::class);
        $stays = $this->stayService->getAllByBookingId($id);
        $guest = '';
        
        if ($booking === null){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render("admin/bookings/view.html.twig",
            [
                'form' => $this->createForm(StayType::class)->createView(),
                'booking' => $booking,
                'guest' => $guest,
                'stays' => $stays,
            ]);
    }
    
    /**
     * @Route("/view/{id}",  methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProcess(Request $request, int $id) 
    {
        $booking = $this->bookingService->getOne($id);
        $form = $this->createForm(StayType::class);
        $stays = $this->stayService->getAllByBookingId($id);
        
        if ($booking === null){
            return $this->redirectToRoute("hotel_index");
        }
        
        $personalNumber = $request->get('personalNumber');
        $guest = $this->guestService->findOneByPersonalNumber($personalNumber);
        
        if ($personalNumber == null) {
            $this->addFlash("errorPersonalNumber", "Please, enter personal number!");
        } else {
            $this->addFlash("infoResult", "Result guest with personal number $personalNumber ");
        }
        
        return $this->render("admin/bookings/view.html.twig",
            [
                'form' => $this->createForm(StayType::class)->createView(),
                'booking' => $booking,
                'guest' => $guest,
                'stays' => $stays,
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_booking_edit", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $booking = $this->bookingService->getOne($id);
        
        $payments = $this->paymentService->getAll();
        $statuses = $this->statusService->getAll();
        $countStay = $this->stayService->getCountByBookingId($id);
        
        if ($booking === null){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/bookings/edit.html.twig',
            [
                'form' => $this->createForm(BookingEditType::class)->createView(),
                'booking' => $booking,
                'payments' => $payments,
                'statuses' => $statuses,
            ]
        );

    }

    /**
     * @Route("/edit/{id}", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Responsen
     */
    public function editProcess(Request $request, int $id)
    {
        $booking = $this->bookingService->getOne($id);
        
        $payments = $this->paymentService->getAll();
        $statuses = $this->statusService->getAll();
        $countStay = $this->stayService->getCountByBookingId($id);
        
        $form = $this->createForm(BookingEditType::class, $booking);
        $form->remove('checkin');
        $form->remove('checkout');
        $form->handleRequest($request);
        
        // if Status = Canceled, not allow edit booking
        if ($booking->getStatusId() == 2 ) {
            $this->addFlash("errors", "Not allow edit booking with status canceled!");
            return $this->render('admin/bookings/edit.html.twig',
                [
                    'form' => $this->createForm(BookingEditType::class)->createView(),
                    'booking' => $booking,
                    'payments' => $payments,
                    'statuses' => $statuses,
                ]
            );    
        }
        
        // if Status = In Progress, Done (Terminated Early), Done,
        // the total amount must be paid
        if ($booking->getStatusId() > 4 ) {
            if ($booking->getPaidAmount() != $booking->getTotalAmount()) {
                $this->addFlash("errors", "Not allow edit. Awaiting Payment!");
                return $this->render('admin/bookings/edit.html.twig',
                    [
                        'form' => $this->createForm(BookingEditType::class)->createView(),
                        'booking' => $booking,
                        'payments' => $payments,
                        'statuses' => $statuses,
                    ]
                );    
            }
        }
        
        // not allow guest count < stay count
        $countGuest = $booking->getAdults() + $booking->getChildBed();
        if ($countGuest < $countStay) {
            $this->addFlash("errors", "Not allow edit. Remove a stay or increase the number of guests!");
            return $this->render('admin/bookings/edit.html.twig',
                [
                    'form' => $this->createForm(BookingEditType::class)->createView(),
                    'booking' => $booking,
                    'payments' => $payments,
                    'statuses' => $statuses,
                ]
            );    
        }
        
        $this->bookingService->edit($booking);

        return $this->redirectToRoute('admin_booking_view', [ 'id' => $id]);
    }

    /**
     * @Route("/delete/{id}", name="admin_booking_delete")
     * @Security("has_role('ROLE_ADMIN')")
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
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllBookings()
    {
        $bookings = $this->bookingService->getAll();

        return $this->render("admin/bookings/list.html.twig",
            [
                'bookings' => $bookings
            ]);
    }
    
    /**
     * @Route("/find", name="admin_bookings_find", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function findAllBookings()
    {
        $statuses = $this->statusService->getAll();
        $payments = $this->paymentService->getAll();
        
        return $this->render("admin/bookings/find.html.twig",
            [
                'statuses' => $statuses, 
                'payments' => $payments,
            ]);
    }
    
    /**
     * @Route("/find", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function findAllBookingsProcess(Request $request)
    {
        $checkin = ''; $checkout = ''; $dateAdded = '';
        $status = ''; $statusName = ''; $payment = ''; $paymentName = '';
        
        $resMsgCheckin = ''; $resMsgCheckout = ''; $resMsgDateAdded = ''; 
        $resMsgStatus = ''; $resMsgPayment = '';
        
        $statuses = $this->statusService->getAll();
        $payments = $this->paymentService->getAll();
        
        $checkinSearch = $request->get('checkin');
        $checkoutSearch = $request->get('checkout');
        $dateAddedSearch = $request->get('dateAdded');
        $statusSearch = $request->get('status');
        $paymentSearch = $request->get('payment');
        
        if ($checkinSearch != null) {
            $checkin = date("Y-m-d", strtotime($checkinSearch)); 
            $resMsgCheckin =  'checkin = ' .$checkin;
        }
            
        if ($checkoutSearch != null) { 
            $checkout = date("Y-m-d", strtotime($checkoutSearch));
            $resMsgCheckout =  'checkout = ' .$checkout;
        }
        
        if ($dateAddedSearch != null) {
            $dateAdded = date("Y-m-d", strtotime($dateAddedSearch));
            $resMsgDateAdded =  'date added = ' .$dateAdded;
        }
            
        if ($statusSearch != null) {
            $status = $statusSearch;
            $statusName = $this->statusService->getOne($statusSearch)->getName();
            $resMsgStatus =  'status =  ' .$statusName;
        }
        
        if ($paymentSearch != null) {
            $payment = $paymentSearch;
            $paymentName = $this->paymentService->getOne($paymentSearch)->getName();
            $resMsgPayment =  'payment = ' .$paymentName;
        }
        
        if ( ($checkinSearch) || ($checkoutSearch) || ($dateAddedSearch) || ($statusSearch) || ($paymentSearch) ) {
            $this->addFlash("info", "Result bookings where $resMsgCheckin $resMsgCheckout $resMsgDateAdded $resMsgStatus $resMsgPayment");
        }
        
        $bookingsResult = $this->bookingService->findAllByCheckinCheckoutDateAddedStatusPayment($checkin, $checkout, $dateAdded, $status, $payment);
        
        return $this->render("admin/bookings/findResult.html.twig",
            [
                'bookingsResult' => $bookingsResult,
                'statuses' => $statuses, 
                'payments' => $payments,
            ]);
            
    }
    
}
