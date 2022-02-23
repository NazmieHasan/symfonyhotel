<?php

namespace HotelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Booking
 *
 * @ORM\Table(name="bookings")
 * @ORM\Entity(repositoryClass="HotelBundle\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\Category", inversedBy="bookings")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    
     /**
     * @var int
     *
     * @ORM\Column(name="room_id", type="integer")
     */
    private $roomId;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\Room", inversedBy="bookings")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private $room;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checkin", type="date")
     */
    private $checkin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checkout", type="date")
     */
    private $checkout;

    /**
     * @var int
     *
     * @ORM\Column(name="days", type="integer")
     */
    private $days;

    /**
     * @var int
     *
     * @ORM\Column(name="adults", type="integer")
     */
    private $adults;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="childBed", type="boolean", nullable=true)
     */
    private $childBed;

    /**
     * @var string
     *
     * @ORM\Column(name="totalAmount", type="decimal", precision=10, scale=2)
     */
    private $totalAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="payment_id", type="integer")
     */
    private $paymentId;

    /**
     * @var Payment
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\Payment", inversedBy="bookings")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @var string
     *
     * @ORM\Column(name="paidAmount", type="decimal", precision=10, scale=2)
     */
    private $paidAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentAmount", type="decimal", precision=10, scale=2)
     */
    private $paymentAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;


    /**
     * @var int
     *
     * @ORM\Column(name="status_id", type="integer")
     */
    private $statusId;

    /**
     * @var Status
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\Status", inversedBy="bookings")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HotelBundle\Entity\Stay", mappedBy="booking")
     */
    private $stays;
    
    public  function __construct()
    {
        $this->dateAdded = new \DateTime('now');
        $this->stays = new ArrayCollection();
    }
    
    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getRoomId()
    {
        return $this->roomId;
    }

    /**
     * @param int $roomId
     */
    public function setRoomId($roomId)
    {
        $this->roomId = $roomId;
    }

    /**
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param Room $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return int
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param int $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }


    /**
     * @param Payment $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }


    /**
     * Set checkin
     *
     * @param \DateTime $checkin
     *
     * @return Booking
     */
    public function setCheckin($checkin)
    {
        $this->checkin = $checkin;

        return $this;
    }

    /**
     * Get checkin
     *
     * @return \DateTime
     */
    public function getCheckin()
    {
        return $this->checkin;
    }

    /**
     * Set checkout
     *
     * @param \DateTime $checkout
     *
     * @return Booking
     */
    public function setCheckout($checkout)
    {
        $this->checkout = $checkout;

        return $this;
    }

    /**
     * Get checkout
     *
     * @return \DateTime
     */
    public function getCheckout()
    {
        return $this->checkout;
    }

    /**
     * Set days
     *
     * @param integer $days
     *
     * @return Booking
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Get days
     *
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set adults
     *
     * @param integer $adults
     *
     * @return Booking
     */
    public function setAdults($adults)
    {
        $this->adults = $adults;

        return $this;
    }

    /**
     * Get adults
     *
     * @return int
     */
    public function getAdults()
    {
        return $this->adults;
    }
    
    /**
     * Set childBed
     *
     * @param bool $childBed
     *
     * @return Booking
     */
    public function setChildBed($childBed)
    {
        $this->childBed = $childBed;

        return $this;
    }

    /**
     * Get childBed
     *
     * @return bool
     */
    public function getChildBed()
    {
        return $this->childBed;
    }
    

    /**
     * Set totalAmount
     *
     * @param string $totalAmount
     *
     * @return Booking
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return string
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set paidAmount
     *
     * @param string $paidAmount
     *
     * @return Booking
     */
    public function setPaidAmount($paidAmount)
    {
        $this->paidAmount = $paidAmount;

        return $this;
    }

    /**
     * Get paidAmount
     *
     * @return string
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * Set paymentAmount
     *
     * @param string $paymentAmount
     *
     * @return Booking
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * Get paymentAmount
     *
     * @return string
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * Set dateAdded
     *
     * @param DateTime $dateAdded
     *
     * @return Booking
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }

    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param Status $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param User $user
     * @return Booking
     */
    public function setUserId(User $userId = null)
    {
        $this->userId = $userId;
        return $this;
    }
    

}
