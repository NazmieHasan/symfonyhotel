<?php

namespace HotelBundle\Service\Stays;

use HotelBundle\Entity\Stay;
use HotelBundle\Repository\StayRepository;
use HotelBundle\Service\Guests\GuestServiceInterface;
use HotelBundle\Service\Bookings\BookingServiceInterface;
use HotelBundle\Service\Rooms\RoomServiceInterface;
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
     * @var BookingServiceInterface
     */
    private $bookingService;
    
    /**
     * @var RoomServiceInterface
     */
    private $roomService;


    /**
     * StayService constructor.
     * @param StayRepository $stayRepository
     * @param GuestRepository $guestRepository
     * @param BookingRepository $bookingRepository
     * @param RoomRepository $roomRepository
     */
    public function __construct(StayRepository $stayRepository,
            GuestServiceInterface $guestService,
            BookingServiceInterface $bookingService,
            RoomServiceInterface $roomService)
    {
        $this->stayRepository = $stayRepository;
        $this->guestService = $guestService;
        $this->bookingService = $bookingService;
        $this->roomService = $roomService;
    }

    /**
     * @param Request $request
     * @param Stay $stay
     * @param int $bookingId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Stay $stay, int $bookingId): bool
    {
        $stay
            ->setBooking($this->bookingService->getOne($bookingId));
        
        $booking = $this->bookingService->getOne($bookingId);
        $stay->setIsTerminated(0);
        $booking->setStatusId(5); // Status In Progress   
            
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
        $bookingId = $stay->getBookingId();
        $booking = $this->bookingService->getOne($bookingId);
        
        if ($stay->getIsTerminated(1)) {  
            if ($stay->getDateOfDeparture() === null) {
                $stay->setDateOfDeparture(new \DateTime('now'));
                $booking->setTerminatedCount($booking->getTerminatedCount() + 1);
                if ( $booking->getGuestCount() == $booking->getTerminatedCount() ) {
                    $maxDateOfDeparture = new \DateTime($stay->getDateOfDeparture()->format('Y-m-d'));
                    $dateCheckout = new \DateTime($booking->getCheckout()->format('Y-m-d'));
                    $daysDiff = $maxDateOfDeparture->diff($dateCheckout)->format("%a");
                    if ($daysDiff > 0) {
                        $booking->setStatusId(6); // Status Terminated Early
                    }
                    if ($daysDiff == 0) {
                        $booking->setStatusId(7); // Status Done 
                    }
                }
            }
        } else {
            if ($stay->getDateOfDeparture() !== null) {
                $stay->setDateOfDeparture(null);
                $booking->setTerminatedCount($booking->getTerminatedCount() - 1);
                $booking->setStatusId(5); // Status In Progress
            }
        }
        
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
    
    /**
     * @param int $bookingId
     * @return Stay[]
     */
    public function getAllByBookingId(int $bookingId)
    {
        $booking = $this->bookingService->getOne($bookingId);
        
        return $this
            ->stayRepository
            ->findBy(['booking' => $booking], ['id' => 'DESC']);
    }
    
    /**
     * @param int $bookingId
     */
    public function getCountByBookingId(int $bookingId)
    {
        $booking = $this->bookingService->getOne($bookingId);
        
        return $this
            ->stayRepository
            ->count(['booking' => $booking]);
    }
    
}
