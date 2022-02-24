<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Room;
use HotelBundle\Entity\Booking;
use HotelBundle\Entity\Stay;
use HotelBundle\Entity\Category;
use HotelBundle\Form\RoomType;
use HotelBundle\Form\BookingType;
use HotelBundle\Form\StayType;
use HotelBundle\Form\CategoryType;
use HotelBundle\Service\Rooms\RoomServiceInterface;
use HotelBundle\Service\Bookings\BookingServiceInterface;
use HotelBundle\Service\Stays\StayServiceInterface;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/rooms")
 * Class RoomController
 * @package HotelBundle\Controller\Admin
 */
class RoomController extends Controller
{
    /**
     * @var RoomServiceInterface
     */
    private $roomService;
    
    /**
     * @var BookingServiceInterface
     */
    private $bookingService;
    
    /**
     * @var StayServiceInterface
     */
    private $stayService;
    
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;
    
    /**
     * RoomController constructor.
     * @param RoomServiceInterface $roomService
     * @param BookingServiceInterface $bookingService
     * @param StayServiceInterface $stayService
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(
        RoomServiceInterface $roomService,
        BookingServiceInterface $bookingService,
        StayServiceInterface $stayService,
        CategoryServiceInterface $categoryService)
    {
        $this->roomService = $roomService;
        $this->bookingService = $bookingService;
        $this->stayService = $stayService;
        $this->categoryService = $categoryService;
    }
    
    /**
     * @Route("/create", name="admin_room_create", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('admin/rooms/create.html.twig',
            ['form' => $this
                ->createForm(RoomType::class)
                ->createView()]);
    }
    
    /**
     * @Route("/create", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request)
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        
        $this->roomService->create($room);
        $this->addFlash("info", "Create room successfully!");
        return $this->redirectToRoute("admin_rooms");
    }
    
    /**
     * @Route("/view/{id}", name="admin_room_view")
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) 
    {
        $room = $this->roomService->getOne($id);
        
        $bookings = $this->bookingService->getAllByRoomId($id);

        return $this->render("admin/rooms/view.html.twig",
            [
                'room' => $room,
                'bookings' => $bookings
            ]);
    }
    
    /**
     * @Route("/", name="admin_rooms")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllRooms(Request $request)
    {
        $rooms = $this->roomService->getAll();
        
        if($request->isMethod("POST")) {  
            $number = $request->get('number');
        
            $em = $this->getDoctrine()->getManager();
            $rooms = $em->getRepository("HotelBundle:Room")
                       ->findBy(
                           [
                               'number' => $number
                           ]);
        }

        return $this->render("admin/rooms/list.html.twig",
            [
                'rooms' => $rooms
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_room_edit", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $room = $this->roomService->getOne($id);
        
        $categories = $this->categoryService->getAll();
        
        if (null === $room){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/rooms/edit.html.twig',
            [
                'form' => $this->createForm(RoomType::class)
                       ->createView(),
                'room' => $room,
                'categories' => $categories,
            ]);

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
        $room = $this->roomService->getOne($id);
        
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        
        $this->roomService->edit($room);

        return $this->redirectToRoute('admin_room_view', [ 'id' => $id]);
    }
    
    /**
     * @Route("/delete/{id}", name="admin_room_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $room = $this->roomService->getOne($id);

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        $this->roomService->delete($room);
        $this->addFlash("info", "Room is deleted");
        
        return $this->redirectToRoute("admin_rooms");
    }
    
}
