<?php

namespace HotelBundle\Service\Bookings;

use HotelBundle\Entity\Booking;
use HotelBundle\Repository\BookingRepository;
use HotelBundle\Service\Users\UserServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

class BookingService implements BookingServiceInterface
{

    private $bookingRepository;
    private $userService;

    /**
     * BookingService constructor.
     * @param BookingRepository $bookingRepository
     * @param UserServiceInterface $userService
     */
    public function __construct(BookingRepository $bookingRepository,
              UserServiceInterface $userService)
    {
        $this->bookingRepository = $bookingRepository;
        $this->userService = $userService;
    }

    /**
     * @param Booking $booking
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Booking $booking): bool
    {
        $client = $this->userService->currentUser();
        $booking->setClient($client);

        return $this->bookingRepository->insert($booking);
    }


    /**
     * @param Booking $booking
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Booking $booking): bool
    {
        return $this->bookingRepository->update($booking);
    }


    /**
     * @param Booking $booking
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Booking $booking): bool
    {
        return $this->bookingRepository->remove($booking);
    }

    /**
     * @return ArrayCollection[]
     */
    public function getAll()
    {
        return $this->bookingRepository->findAll();
    }

    /**
     * @param int $id
     * @return Booking|null|object
     */
    public function getOne(int $id): ?Booking
    {
        return $this->bookingRepository->find($id);
    }

    /**
     * @return ArrayCollection|Booking[]
     */
    public function getAllBookingsByClient()
    {
        return  $this->bookingRepository

            ->findBy(
                ['client' => $this->userService->currentUser()],
                ['dateAdded' => 'DESC']
            );

    }
}