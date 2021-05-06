<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Status;
use HotelBundle\Form\StatusType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/statuses")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class StatusController extends Controller
{
    /**
     * @Route("/create", name="admin_status_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function create(Request $request)
    {
        $status = new Status();
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($status);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/statuses/create.html.twig',
            ['form' => $form->createView()]);
    }
}
