<?php

namespace HotelBundle\Service\Statuses;

use HotelBundle\Entity\Status;
use Doctrine\Common\Collections\ArrayCollection;

interface StatusServiceInterface
{
    public function create(Status $status) : bool;
    public function edit(Status $status) : bool;
    public function delete(Status $status) : bool;
    public function getOne(int $id) : ?Status;
    
    /**
     * @return ArrayCollection|Status[]
     */
    public function getAll();
}