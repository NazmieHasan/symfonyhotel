<?php

namespace HotelBundle\Service\Bookings;

use HotelBundle\Entity\Booking;
use HotelBundle\Repository\BookingRepository;
use HotelBundle\Service\Users\UserServiceInterface;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use HotelBundle\Service\Rooms\RoomServiceInterface;
use HotelBundle\Service\Statuses\StatusServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

class BookingService implements BookingServiceInterface
{

    private $bookingRepository;
    
    /**
     * @var UserServiceInterface
     */ 
    private $userService;
    
    /**
     * @var CategoryServiceInterface
     */ 
    private $categoryService;
    
    /**
     * @var RoomServiceInterface
     */
    private $roomService;
    
    /**
     * @var StatusServiceInterface
     */ 
    private $statusService;

    /**
     * BookingService constructor.
     * @param BookingRepository $bookingRepository
     * @param UserServiceInterface $userService
     * @param CategoryServiceInterface $categoryService
     * @param RoomServiceInterface $roomService
     * @param StatusServiceInterface $statusService
     */
    public function __construct(BookingRepository $bookingRepository,
              UserServiceInterface $userService,
              CategoryServiceInterface $categoryService,
              RoomServiceInterface $roomService,
              StatusServiceInterface $statusService)
    {
        $this->bookingRepository = $bookingRepository;
        $this->userService = $userService;
        $this->categoryService = $categoryService;
        $this->roomService = $roomService;
        $this->statusService = $statusService;
    }
    
    /**
     * @param Booking $booking
     * @param int $categoryId
     * @param int $roomId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Booking $booking, int $categoryId, int $roomId): bool
    {
        $userId = $this->userService->currentUser();
        $booking->setUserId($userId);
        $booking->setCategory($this->categoryService->getOne($categoryId));
        $booking->setRoom($this->roomService->getOne($roomId));
        $booking->setTerminatedCount(0);
       
        $days = $booking->getCheckin()->diff($booking->getCheckout())->format("%a");
        $booking->setDays($days);
        $booking->setTotalAmount(($booking->getCategory()->getPrice()) * ($booking->getDays()));
        $booking->setPaidAmount(0.00);
        $booking->setPaymentAmount($booking->getTotalAmount() - $booking->getPaidAmount());
        
        $booking->setStatus($this->statusService->getOne(1)); // Status Awaiting Payment
        
        $guestCount = $booking->getAdults() + $booking->getChildBed();
        $booking->setGuestCount($guestCount);

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
        $booking->setPaymentAmount($booking->getTotalAmount() - $booking->getPaidAmount());
        
        $guestCount = $booking->getAdults() + $booking->getChildBed();
        $booking->setGuestCount($guestCount);
        
        if ($booking->getStatusId() == 1) {
            if ($booking->getPaidAmount() == $booking->getTotalAmount() * 0.40) {
                $booking->setStatusId(3); // Status For Execution (40% paid)
            }
        }
        
        if ( ($booking->getStatusId() == 1) or ($booking->getStatusId() == 3) ) {
            if ($booking->getPaidAmount() == $booking->getTotalAmount()) {
                $booking->setStatusId(4); // Status For Execution (100% paid)
            }
        }
       
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
        return $this->bookingRepository->findBy([], ['dateAdded' => 'DESC']);
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
    public function getAllByCurrentUser()
    {
        return  $this->bookingRepository

            ->findBy(
                ['userId' => $this->userService->currentUser()],
                ['dateAdded' => 'DESC']
            );

    }
    
    /**
     * @param int $userId
     * @return Booking[]
     */
    public function getAllByUserId(int $userId)
    {
        $user = $this->userService->findOneById($userId);
        
        return $this
            ->bookingRepository
            ->findBy(['userId' => $userId], ['id' => 'DESC']);

    }
    
    /**
     * @param int $roomId
     * @return Booking[]
     */
    public function getAllByRoomId(int $roomId)
    {
        $room = $this->roomService->getOne($roomId);
        
        return $this
            ->bookingRepository
            ->findBy(['roomId' => $roomId], ['id' => 'DESC']);
    }
    
    public function findAllByCheckinCheckoutDateAddedStatusPayment($checkin, $checkout, $dateAdded, $status, $payment)
    {
        return $this->bookingRepository->getAllByCheckinCheckoutDateAddedStatusPayment($checkin, $checkout, $dateAdded, $status, $payment);
    }
   
}
