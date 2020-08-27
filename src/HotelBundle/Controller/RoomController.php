<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Room;
use HotelBundle\Form\RoomType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends Controller
{
    /**
     * @Route("/createroom", name="room_create")
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

        return $this->render('rooms/create.html.twig',
            ['form' => $form->createView()]);
    }
}
