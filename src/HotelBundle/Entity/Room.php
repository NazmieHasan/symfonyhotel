<?php

namespace HotelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table(name="rooms")
 * @ORM\Entity(repositoryClass="HotelBundle\Repository\RoomRepository")
 */
class Room
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
     * @ORM\Column(name="number", type="string")
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer")
     */
    private $categoryId;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="HotelBundle\Entity\Category", inversedBy="rooms")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HotelBundle\Entity\Stay", mappedBy="room")
     */
    private $stays;

    public function __construct()
    {
        $this->stays = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->getId();
        return $this->getNumber();
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
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
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
     * @return ArrayCollection
     */
    public function getStays()
    {
        return $this->stays;
    }

    /**
     * @param ArrayCollection $stays
     */
    public function setStays(ArrayCollection $stays)
    {
        $this->stays = $stays;
    }


}