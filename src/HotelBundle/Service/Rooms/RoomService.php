<?php

namespace HotelBundle\Service\Rooms;

use HotelBundle\Entity\Room;
use HotelBundle\Repository\RoomRepository;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

class RoomService implements RoomServiceInterface
{

    private $roomRepository;
    
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    /**
     * RoomService constructor.
     * @param RoomRepository $roomRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(RoomRepository $roomRepository,
            CategoryServiceInterface $categoryService)
    {
        $this->roomRepository = $roomRepository;
        $this->categoryService = $categoryService;
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
    
    /**
     * @param int $categoryId
     * @return Room[]
     */
    public function getAllByCategoryId(int $categoryId)
    {
        $category = $this->categoryService->getOne($categoryId);
        
        return $this
            ->roomRepository
            ->findBy(['category' => $category]);
    }
    
    /**
     * @param date $checkin
     * @param date $checkout
     * @param int $categoryId
     * @return Room[]
     */
    public function getAllFreeRoomsByCheckinCheckoutCategoryId(date $checkin, date $checkout, int $categoryId)
    {
        // TODO 
        
    } 
    
}