<?php

namespace HotelBundle\Service\Stays;

use HotelBundle\Entity\Stay;
use Doctrine\Common\Collections\ArrayCollection;

interface StayServiceInterface
{
    public function create(Stay $stay) : bool;
    public function edit(Stay $stay) : bool;
    public function delete(Stay $stay) : bool;
    public function getOne(int $id) : ?Stay;
    
    /**
     * @return ArrayCollection|Stay[]
     */
    public function getAll();
}