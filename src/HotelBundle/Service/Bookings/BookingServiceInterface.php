<?php

namespace HotelBundle\Service\Bookings;


use HotelBundle\Entity\Booking;
use Doctrine\Common\Collections\ArrayCollection;

interface BookingServiceInterface
{
    public function create(Booking $booking, int $categoryId, int $roomdId) : bool;
    public function edit(Booking $booking) : bool;
    public function delete(Booking $booking) : bool;
    public function getOne(int $id) : ?Booking;
    
    /**
     * @return ArrayCollection|Booking[]
     */
    public function getAll();

    /**
     * @return ArrayCollection|Booking[]
     */
    public function getAllByCurrentUser();
    
    /**
     * @param int $userId
     * @return Booking[]
     */
    public function getAllByUserId(int $userId);
    
    /**
     * @param int $roomId
     * @return Booking[]
     */
    public function getAllByRoomId(int $roomId);
    
    public function findAllByCheckinCheckoutDateAddedStatusPayment($checkin, $checkout, $dateAdded, $status, $payment);
    
}
