<?php

namespace HotelBundle\Service\Roles;

use HotelBundle\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;

interface RoleServiceInterface
{
    public function create(Role $role) : bool;
    
    /**
     * @return ArrayCollection|Role[]
     */
    public function getAll();

    public function findOneBy(string $name) : ?Role; 

}