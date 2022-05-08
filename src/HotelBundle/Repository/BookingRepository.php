<?php

namespace HotelBundle\Repository;

use HotelBundle\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;

/**
 * BookingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookingRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(EntityManagerInterface $em,
                                Mapping\ClassMetadata $metaData = null)
    {
        parent::__construct($em,
            $metaData == null ?
                new Mapping\ClassMetadata(Booking::class) :
                $metaData);
    }

    /**
     * @param Booking $booking
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insert(Booking $booking)
    {
        $this->_em->persist($booking);

        try {
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }


    /**
     * @param Booking $booking
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Booking $booking)
    {
        try {
            $this->_em->merge($booking);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    /**
     * @param Booking $booking
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Booking $booking)
    {
        $this->_em->remove($booking);
        try {
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
    
    public function getAllByCheckinCheckoutDateAddedPaymentStatus($checkin, $checkout, $dateAdded, $payment, $status)
    {
        return $this->createQueryBuilder('b')
            ->addSelect('s')
            ->innerJoin("b.status", 's')
            ->innerJoin("b.payment", 'p')
            ->andwhere('s.id = :status')
            ->andwhere('p.id = :payment')
            ->andWhere('b.checkin = :checkin')
            ->andWhere('b.checkout = :checkout')
            ->andWhere('b.dateAdded like :dateAdded')
            ->setParameter('status', $status)
            ->setParameter('payment', $payment)
            ->setParameter('checkin', $checkin)
            ->setParameter('checkout', $checkout)
            ->setParameter('dateAdded', '%'.$dateAdded.'%')
            ->getQuery()
            ->getResult();
    }

}
