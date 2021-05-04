<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Booking;
use HotelBundle\Entity\User;
use HotelBundle\Form\BookingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/bookings")
 * Class BookingController
 * @package HotelBundle\Controller\Admin
 */
class BookingController extends Controller
{
    /**
     * @Route("/create", name="admin_booking_create")
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
            $booking->setUserId($this->getUser());
            $booking->setPaidAmount(00.00);
            $booking->setPaymentAmount(00.00);
            $booking->setTotalAmount(00.00);
            $booking->setDays(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $em->flush();

            $this->addFlash("info", "Create booking successfully!");
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('bookings/create.html.twig',
            ['form' => $form->createView()]);
    }


    /**
     * @Route("/edit/{id}", name="admin_booking_edit")
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


        if(!$this->isUserOrAdmin($booking)){
            return $this->redirectToRoute("hotel_index");
        }

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            $booking->setUserId($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->merge($booking);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/bookings/edit.html.twig',
            [
                'form' => $form->createView(),
                'booking' => $booking
            ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_booking_delete")
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

        if(!$this->isUserOrAdmin($booking)){
            return $this->redirectToRoute("hotel_index");
        }


        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            $booking->setUserId($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->remove($booking);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/bookings/delete.html.twig',
            [
                'form' => $form->createView(),
                'booking' => $booking
            ]);
    }

    /**
     * @Route("/view/{id}", name="admin_booking_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) {
        $booking = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->find($id);

        return $this->render("admin/bookings/view.html.twig",
            ['booking' => $booking ]);

    }

    /**
     * @param Booking $booking
     * @return bool
     */
    private function isUserOrAdmin(Booking $booking)
    {   /** @var User $currentUser */
        $currentUser = $this->getUser();

        if(!$currentUser->isUser($booking) && !$currentUser->isAdmin()) {
            return false;
        }
        return true;
    }

    /**
     * @Route("/users/my_bookings", name="my_bookings")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllBookingsByUser(){
        $bookings = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->findBy(
                ['userId' => $this->getUser()],
                [
                    'dateAdded' => 'DESC'
                ]);

        return $this->render("users/myBookings.html.twig",
            [
                'bookings' => $bookings
            ]);

    }

    /**
     * @Route("/", name="admin_bookings")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllBookings(){
        $bookings = $this
            ->getDoctrine()
            ->getRepository(Booking::class)
            ->findBy(
                [],
                [
                    'dateAdded' => 'DESC'
                ]);

        return $this->render("admin/bookings/list.html.twig",
            [
                'bookings' => $bookings
            ]);

    }


}