<?php

namespace HotelBundle\Service\Statuses;

use HotelBundle\Entity\Status;
use HotelBundle\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;

class StatusService implements StatusServiceInterface
{

    private $statusRepository;

    /**
     * StatusService constructor.
     * @param StatusRepository $statusRepository
     */
    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    /**
     * @param Status $status
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Status $status): bool
    {
        return $this->statusRepository->insert($status);
    }
    
    /**
     * @param Status $status
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Status $status): bool
    {
        return $this->statusRepository->update($status);
    }

    /**
     * @param Status $status
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Status $status): bool
    {
        return $this->statusRepository->remove($status);
    }
    
    /**
     * @param int $id
     * @return Status|null|object
     */
    public function getOne(int $id): ?Status
    {
        return $this->statusRepository->find($id);
    }

    /**
     * @return ArrayCollection[]
     */
    public function getAll()
    {
        return $this->statusRepository->findBy([], ['id' => 'ASC']);
    }
    
}