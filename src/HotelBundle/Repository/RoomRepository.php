<?php

namespace HotelBundle\Repository;

use HotelBundle\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;

/**
 * RoomRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RoomRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(EntityManagerInterface $em,
                                Mapping\ClassMetadata $metaData = null)
    {
        parent::__construct($em,
            $metaData == null ?
                new Mapping\ClassMetadata(Room::class) :
                $metaData);
    }

    /**
     * @param Room $room
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insert(Room $room)
    {
        $this->_em->persist($room);

        try {
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }


    /**
     * @param Room $room
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Room $room)
    {
        try {
            $this->_em->merge($room);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    /**
     * @param Room $room
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Room $room)
    {
        $this->_em->remove($room);
        try {
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
    
}
