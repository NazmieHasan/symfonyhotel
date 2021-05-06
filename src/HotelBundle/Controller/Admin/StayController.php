<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Stay;
use HotelBundle\Form\StayType;
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
     * @Route("/create", name="admin_stay_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $stay = new Stay();
        $form = $this->createForm(StayType::class, $stay);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stay);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/stays/create.html.twig',
            ['form' => $form->createView()]);
    }
}

