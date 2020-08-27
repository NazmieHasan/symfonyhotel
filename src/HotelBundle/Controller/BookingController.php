<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Booking;
use HotelBundle\Form\BookingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends Controller
{
    /**
     * @Route("/createbooking", name="booking_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @noinspection PhpParamsInspection
     */
    public function create(Request $request)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            $booking->setClient($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('bookings/create.html.twig',
            ['form' => $form->createView()]);
    }


    /**
     * @Route("/edit/{id}", name="booking_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @noinspection PhpParamsInspection
     */
    public function edit(Request $request, $id)
    {
        $booking = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->find($id);
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            $booking->setClient($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->merge($booking);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('bookings/edit.html.twig',
            [
                'form' => $form->createView(),
                'booking' => $booking
            ]);
    }


    /**
     * @Route("/delete/{id}", name="booking_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @noinspection PhpParamsInspection
     */
    public function delete(Request $request, $id)
    {
        $booking = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->find($id);
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            $booking->setClient($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->remove($booking);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('bookings/delete.html.twig',
            [
                'form' => $form->createView(),
                'booking' => $booking
            ]);
    }




    /**
     * @Route("/booking/{id}", name="booking_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view($id) {
        $booking = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->find($id);

        return $this->render("bookings/view.html.twig",
        ['booking' => $booking ]);

    }

}