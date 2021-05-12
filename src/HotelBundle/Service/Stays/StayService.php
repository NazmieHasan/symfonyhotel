<?php

namespace HotelBundle\Service\Stays;

use HotelBundle\Entity\Stay;
use HotelBundle\Repository\StayRepository;
use HotelBundle\Service\Guests\GuestServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class StayService implements StayServiceInterface
{
    private $stayRepository;
    
    /**
     * @var GuestServiceInterface
     */
    private $guestService;

    /**
     * StayService constructor.
     * @param StayRepository $stayRepository
     */
    public function __construct(StayRepository $stayRepository,
            GuestServiceInterface $guestService)
    {
        $this->stayRepository = $stayRepository;
        $this->guestService = $guestService;
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
        return $this->stayRepository->findBy([], ['id' => 'DESC']);
    }
    
    /**
     * @param int $guestId
     * @return Stay[]
     */
    public function getAllByGuestId(int $guestId)
    {
        $guest = $this->guestService->getOne($guestId);
        return $this
            ->stayRepository
            ->findBy(['guest' => $guest], ['id' => 'DESC']);
    }
    
}