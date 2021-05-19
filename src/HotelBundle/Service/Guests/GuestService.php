<?php

namespace HotelBundle\Service\Guests;

use HotelBundle\Entity\Guest;
use HotelBundle\Repository\GuestRepository;
use Doctrine\Common\Collections\ArrayCollection;

class GuestService implements GuestServiceInterface
{

    private $guestRepository;

    /**
     * GuestService constructor.
     * @param GuestRepository $guestRepository
     */
    public function __construct(GuestRepository $guestRepository)
    {
        $this->guestRepository = $guestRepository;
    }

    /**
     * @param Guest $guest
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Guest $guest): bool
    {
        return $this->guestRepository->insert($guest);
    }
    
    /**
     * @param Guest $guest
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Guest $guest): bool
    {
        return $this->guestRepository->update($guest);
    }

    /**
     * @param Guest $guest
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Guest $guest): bool
    {
        return $this->guestRepository->remove($guest);
    }
    
    /**
     * @param int $id
     * @return Guest|null|object
     */
    public function getOne(int $id): ?Guest
    {
        return $this->guestRepository->find($id);
    }

    /**
     * @return ArrayCollection[]
     */
    public function getAll()
    {
        return $this->guestRepository->findBy([], ['id' => 'DESC']);
    }
    
}