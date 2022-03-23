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
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Booking $booking, int $categoryId): bool
    {
        $userId = $this->userService->currentUser();
        $booking->setUserId($userId);
        $booking->setCategory($this->categoryService->getOne($categoryId));
        $booking->setTerminatedCount(0);
        
        // $roomId = $this->roomService->getOneFreeRoomByCheckinCheckoutCategoryId(date $checkin, date $checkout, int $categoryId);
        // $booking->setRoom($roomId);
        
        $firstRoomId = $this->roomService->getFirstByCategoryId($categoryId)->getId();
        $lastRoomId = $this->roomService->getLastByCategoryId($categoryId)->getId();
        $id = rand($firstRoomId, $lastRoomId);
        $roomId = $this->roomService->getOne($id);
        $booking->setRoomId($roomId);

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
        $days = $booking->getCheckin()->diff($booking->getCheckout())->format("%a");
        $booking->setDays($days);
        $booking->setTotalAmount(($booking->getCategory()->getPrice()) * ($booking->getDays()));
        $booking->setPaymentAmount($booking->getTotalAmount() - $booking->getPaidAmount());
        
        $guestCount = $booking->getAdults() + $booking->getChildBed();
        $booking->setGuestCount($guestCount);
        
        if ($booking->getPaidAmount() === $booking->getTotalAmount() * 0.40) {
            $booking->setStatusId(3); // Status For Execution (40% paid)
        }
        
        if ($booking->getPaidAmount() === $booking->getTotalAmount()) {
            $booking->setStatusId(4); // Status For Execution (100% paid)
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
    
}
