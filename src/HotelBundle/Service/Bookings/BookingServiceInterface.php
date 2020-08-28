<?php

namespace HotelBundle\Service\Bookings;


use HotelBundle\Entity\Booking;
use Doctrine\Common\Collections\ArrayCollection;

interface BookingServiceInterface
{
    /**
     * @return ArrayCollection|Booking[]
     */
    public function getAll();
    public function create(Booking $booking) : bool;
    public function edit(Booking $booking) : bool;
    public function delete(Booking $booking) : bool;
    public function getOne(int $id) : ?Booking;

    /**
     * @return ArrayCollection|Booking[]
     */
    public function getAllBookingsByClient();
}