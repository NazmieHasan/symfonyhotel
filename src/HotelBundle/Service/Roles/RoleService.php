<?php

namespace HotelBundle\Service\Roles;

use HotelBundle\Entity\Role;
use HotelBundle\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;

class RoleService implements RoleServiceInterface
{

    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
    
    /**
     * @param Role $role
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Role $role): bool
    {
        return $this->roleRepository->insert($role);
    }
    
    /**
     * @return ArrayCollection[]
     */
    public function getAll()
    {
        return $this->roleRepository->findBy([], ['id' => 'DESC']);
    }

    /**
     * @param string $name
     * @return Role|null|object
     */
    public function findOneBy(string $name): ?Role
    {
        return $this->roleRepository->findOneBy(
            ['name' => $name]
        );

    }
    
    public function getCount()
    {
        return $this->roleRepository->count([]);
    }
    
}
