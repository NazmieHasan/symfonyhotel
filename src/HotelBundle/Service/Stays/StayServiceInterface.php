<?php

namespace HotelBundle\Service\Stays;

use HotelBundle\Entity\Stay;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

interface StayServiceInterface
{
    public function create(Stay $stay, int $bookingId) : bool;
    public function edit(Stay $stay) : bool;
    public function delete(Stay $stay) : bool;
    public function getOne(int $id) : ?Stay;
    
    /**
     * @return ArrayCollection|Stay[]
     */
    public function getAll();
    
    /**
     * @param int $guestId
     * @return Stay[]
     */
    public function getAllByGuestId(int $guestId);
    
    /**
     * @param int $bookingId
     * @return Stay[]
     */
    public function getAllByBookingId(int $bookingId);
    
}