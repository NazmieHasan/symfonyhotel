<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Stay;
use HotelBundle\Entity\Booking;
use HotelBundle\Entity\Guest;
use HotelBundle\Form\StayType;
use HotelBundle\Form\StayEditType;
use HotelBundle\Form\BookingType;
use HotelBundle\Form\GuestType;
use HotelBundle\Service\Stays\StayServiceInterface;
use HotelBundle\Service\Bookings\BookingServiceInterface;
use HotelBundle\Service\Guests\GuestServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/stays")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class StayController extends Controller
{
    /**
     * @var StayServiceInterface
     */
    private $stayService;
    
    /**
     * @var BookingServiceInterface
     */
    private $bookingService;
    
    /**
     * @var GuestServiceInterface
     */
    private $guestService;
    
    /**
     * StayController constructor.
     * @param StayServiceInterface $stayService
     * @param BookingServiceInterface $bookingService
     * @param GuestServiceInterface $guestService
     */
    public function __construct(
        StayServiceInterface $stayService,
        BookingServiceInterface $bookingService,
        GuestServiceInterface $guestService)
    {
        $this->stayService = $stayService;
        $this->bookingService = $bookingService;
        $this->guestService = $guestService;
    }
    
    /**
     * @Route("/create/{id}", name="admin_stay_create", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, $id)
    {
        $stay = new Stay();
        $form = $this->createForm(StayType::class, $stay);
        $form->handleRequest($request);
        
        $this->stayService->create($stay, $id);
        $this->addFlash("info", "Create stay successfully!");
        return $this->redirectToRoute("admin_booking_view", ['id' => $id]);
    }
    
    /**
     * @Route("/", name="admin_stays")
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllStays()
    {
        $stays = $this->stayService->getAll();

        return $this->render("admin/stays/list.html.twig",
            [
                'stays' => $stays
            ]);
    }
    
    /**
     * @Route("/view/{id}", name="admin_stay_view")
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) {
        $stay = $this->stayService->getOne($id);

        return $this->render("admin/stays/view.html.twig",
            [
                'stay' => $stay
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_stay_edit", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $stay = $this->stayService->getOne($id);
        $bookings = $this->bookingService->getAll();
        $guests = $this->guestService->getAll();
        
        if (null === $stay){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/stays/edit.html.twig',
            [
                'form' => $this->createForm(StayEditType::class)
                       ->createView(),
                'stay' => $stay,
                'bookings' => $bookings,
                'guests' => $guests
            ]);

    }
    
    /**
     * @Route("/edit/{id}", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')"))
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Responsen
     */
    public function editProcess(Request $request, int $id)
    {
        $stay = $this->stayService->getOne($id);
        
        $form = $this->createForm(StayEditType::class, $stay);
        $form->handleRequest($request);
        $this->stayService->edit($stay);

        return $this->redirectToRoute('admin_stay_view', [ 'id' => $id]);
    }
    
    /**
     * @Route("/delete/{id}", name="admin_stay_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $stay = $this->stayService->getOne($id);

        $form = $this->createForm(StayType::class, $stay);
        $form->handleRequest($request);
        $this->stayService->delete($stay);
        $this->addFlash("info", "Stay is deleted");
        
        return $this->redirectToRoute("admin_stays");
    }
    
}
