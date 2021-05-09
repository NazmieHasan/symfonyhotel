<?php

namespace HotelBundle\Service\Stays;

use HotelBundle\Entity\Stay;
use HotelBundle\Repository\StayRepository;
use Doctrine\Common\Collections\ArrayCollection;

class StayService implements StayServiceInterface
{

    private $stayRepository;

    /**
     * StayService constructor.
     * @param StayRepository $stayRepository
     */
    public function __construct(StayRepository $stayRepository)
    {
        $this->stayRepository = $stayRepository;
    }

    /**
     * @param Stay $stay
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Stay $stay): bool
    {
        return $this->stayRepository->insert($stay);
    }
    
    /**
     * @param Stay $stay
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Stay $stay): bool
    {
        return $this->stayRepository->update($stay);
    }

    /**
     * @param Stay $stay
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Stay $stay): bool
    {
        return $this->stayRepository->remove($stay);
    }
    
    /**
     * @param int $id
     * @return Stay|null|object
     */
    public function getOne(int $id): ?Stay
    {
        return $this->stayRepository->find($id);
    }

    /**
     * @return ArrayCollection[]
     */
    public function getAll()
    {
        return $this->stayRepository->findAll();
    }
    
}