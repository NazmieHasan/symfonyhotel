<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Room;
use HotelBundle\Form\RoomType;
use HotelBundle\Service\Rooms\RoomServiceInterface;
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
    private $roomtService;
    
    /**
     * RoomController constructor.
     * @param RoomServiceInterface $roomService
     */
    public function __construct(
        RoomServiceInterface $roomService)
    {
        $this->roomService = $roomService;
    }
    
    /**
     * @Route("/create", name="admin_room_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
     * @Route("/", name="admin_rooms")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllRooms()
    {
        $rooms = $this->roomService->getAll();

        return $this->render("admin/rooms/list.html.twig",
            [
                'rooms' => $rooms
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_room_edit", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $room = $this->roomService->getOne($id);
        
        if (null === $room){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/rooms/edit.html.twig',
            [
                'form' => $this->createForm(RoomType::class)
                       ->createView(),
                'room' => $room
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
        $room = $this->roomService->getOne($id);
        
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        
        $this->roomService->edit($room);

        return $this->redirectToRoute("admin_rooms");
    }
    
    /**
     * @Route("/delete/{id}", name="admin_room_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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