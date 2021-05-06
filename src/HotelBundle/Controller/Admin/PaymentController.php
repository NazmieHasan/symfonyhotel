<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Payment;
use HotelBundle\Form\PaymentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/payments")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class PaymentController extends Controller
{
    /**
     * @Route("/create", name="admin_payment_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/payments/create.html.twig',
            ['form' => $form->createView()]);
    }
}

