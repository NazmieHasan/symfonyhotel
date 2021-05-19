<?php

namespace HotelBundle\Service\Rooms;

use HotelBundle\Entity\Room;
use HotelBundle\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;

class RoomService implements RoomServiceInterface
{

    private $roomRepository;

    /**
     * RoomService constructor.
     * @param RoomRepository $roomRepository
     */
    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    /**
     * @param Room $room
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Room $room): bool
    {
        return $this->roomRepository->insert($room);
    }
    
    /**
     * @param Room $room
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Room $room): bool
    {
        return $this->roomRepository->update($room);
    }

    /**
     * @param Room $room
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Room $room): bool
    {
        return $this->roomRepository->remove($room);
    }
    
    /**
     * @param int $id
     * @return Room|null|object
     */
    public function getOne(int $id): ?Room
    {
        return $this->roomRepository->find($id);
    }

    /**
     * @return ArrayCollection[]
     */
    public function getAll()
    {
        return $this->roomRepository->findBy([], ['id' => 'DESC']);
    }
    
}