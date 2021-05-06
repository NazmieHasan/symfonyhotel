<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Guest;
use HotelBundle\Form\GuestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/guests")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class GuestController extends Controller
{
    /**
     * @Route("/create", name="admin_guest_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $guest = new Guest();
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($guest);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/guests/create.html.twig',
            ['form' => $form->createView()]);
    }
}