<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Room;
use HotelBundle\Form\RoomType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/rooms")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class RoomController extends Controller
{
    /**
     * @Route("/create", name="admin_room_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function create(Request $request)
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/rooms/create.html.twig',
            ['form' => $form->createView()]);
    }
}
