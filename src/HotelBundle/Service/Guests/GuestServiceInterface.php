<?php

namespace HotelBundle\Service\Guests;

use HotelBundle\Entity\Guest;
use Doctrine\Common\Collections\ArrayCollection;

interface GuestServiceInterface
{
    public function create(Guest $guest) : bool;
    public function edit(Guest $guest) : bool;
    public function delete(Guest $guest) : bool;
    public function getOne(int $id) : ?Guest;
    
    /**
     * @return ArrayCollection|Guest[]
     */
    public function getAll();
    
    public function findOneByPersonalNumber(string $personalNumber) : ?Guest; 
}