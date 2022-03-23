<?php

namespace HotelBundle\Service\Rooms;

use HotelBundle\Entity\Room;
use Doctrine\Common\Collections\ArrayCollection;

interface RoomServiceInterface
{
    public function create(Room $room) : bool;
    public function edit(Room $room) : bool;
    public function delete(Room $room) : bool;
    public function getOne(int $id) : ?Room;
    
    /**
     * @return ArrayCollection|Room[]
     */
    public function getAll();
    
    /**
     * @param int $categoryId
     * @return Room[]
     */
    public function getAllByCategoryId(int $categoryId);
    
    /**
     * @param int $categoryId
     */
    public function getFirstByCategoryId(int $categoryId);
    
    /**
     * @param int $categoryId
     */
    public function getLastByCategoryId(int $categoryId);
    
}
