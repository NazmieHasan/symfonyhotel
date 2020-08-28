<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Booking;
use HotelBundle\Entity\User;
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
            $booking->setPaidAmount(00.00);
            $booking->setPaymentAmount(00.00);
            $booking->setTotalAmount(00.00);
            $booking->setDays(0);
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
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @noinspection PhpParamsInspection
     */
    public function edit(Request $request, int $id)
    {
        $booking = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->find($id);

        if(null === $booking) {
            return $this->redirectToRoute("hotel_index");
        }


        if(!$this->isClientOrAdmin($booking)){
            return $this->redirectToRoute("hotel_index");
        }

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
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @noinspection PhpParamsInspection
     */
    public function delete(Request $request, int $id)
    {
        $booking = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->find($id);

        if(null === $booking) {
            return $this->redirectToRoute("hotel_index");
        }

        if(!$this->isClientOrAdmin($booking)){
            return $this->redirectToRoute("hotel_index");
        }


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

    /**
     * @param Booking $booking
     * @return bool
     */
    private function isClientOrAdmin(Booking $booking)
    {   /** @var User $currentUser */
        $currentUser = $this->getUser();

        if(!$currentUser->isClient($booking) && !$currentUser->isAdmin()) {
            return false;
        }
        return true;
    }

    /**
     * @Route("/bookings/my_bookings", name="my_bookings")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllBookingsByUser(){
        $bookings = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->findBy(['client' => $this->getUser()]);

        return $this->render("bookings/myBookings.html.twig",
        [
            'bookings' => $bookings
        ]);

    }

}