<?php

namespace HotelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Stay
 *
 * @ORM\Table(name="stays")
 * @ORM\Entity(repositoryClass="HotelBundle\Repository\PaymentRepository")
 */
class Stay
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
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     */
    private $note;
    
     /**
     * @var int
     *
     * @ORM\Column(name="guest_id", type="integer")
     */
    private $guestId;

    /**
     * @var Guest
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\Guest", inversedBy="stays")
     * @ORM\JoinColumn(name="guest_id", referencedColumnName="id")
     */
    private $guest;
    
     /**
     * @var int
     *
     * @ORM\Column(name="room_id", type="integer")
     */
    private $roomId;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\Room", inversedBy="stays")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    private $room;
    
    /**
     * @var int
     *
     * @ORM\Column(name="booking_id", type="integer")
     */
    private $bookingId;
    
    /**
     * @var Booking
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\Booking", inversedBy="stays")
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id")
     */
    private $booking;
    

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
     * Set name
     *
     * @param string $note
     *
     * @return Stay
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
    
    /**
     * @return int
     */
    public function getGuestId()
    {
        return $this->guestId;
    }

    /**
     * @param int $guestId
     */
    public function setGuestId($guestId)
    {
        $this->guestId = $guestId;
    }

    /**
     * @return Guest
     */
    public function getGuest()
    {
        return $this->guest;
    }

    /**
     * @param Guest $guest
     */
    public function setGuest($guest)
    {
        $this->guest = $guest;
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
    public function getBookingId()
    {
        return $this->bookingId;
    }

    /**
     * @param int $bookingId
     */
    public function setBookingId($bookingId)
    {
        $this->bookingId = $bookingId;
    }
    
    /**
     * @param Booking $booking
     */
    public function setBooking($booking)
    {
        $this->booking = $booking;
    }

    /**
     * @return Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }

}
