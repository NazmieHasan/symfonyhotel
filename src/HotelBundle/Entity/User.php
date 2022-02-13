<?php

namespace HotelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="HotelBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @Assert\NotBlank(message="This field cannot be empty")
     *
     * @Assert\Email(
     *     message="Invalid email",
     *     checkMX=false
     * )
     *
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank(message="This field cannot be empty")
     *
     * @Assert\Length(
     *     min = 5,
     *     max = 15,
     *     minMessage="Min length is 5",
     *     maxMessage="Max lenth is 15"
     * )
     *
     *  * @Assert\Regex(
     *     pattern = "/^[A-Za-z0-9]+$/",
     *     match=true,
     *     message="The password must contain only letters and digits"
     * )*
     *
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @Assert\NotBlank(message="This field cannot be empty")
     *
     * @Assert\Length(
     *     min = 3,
     *     max = 15,
     *     minMessage="Login name min length is 3",
     *     maxMessage="Login name max length is 15"
     * )
     *
     * @Assert\Regex(
     *     pattern = "/^[A-Z]{1}[a-z]+$/",
     *     match=true,
     *     message="Login name must start with a capital letter, followed by lowercase letters"
     * )
     *
     * @var string
     *
     * @ORM\Column(name="loginName", type="string", length=255)
     */
    private $loginName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HotelBundle\Entity\Booking", mappedBy="user")
     */
    private $bookings;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HotelBundle\Entity\Role")
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $roles;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->roles = new ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set loginName
     *
     * @param string $loginName
     *
     * @return User
     */
    public function setLoginName($loginName)
    {
        $this->loginName = $loginName;

        return $this;
    }


    /**
     * Get loginName
     *
     * @return string
     */
    public function getLoginName()
    {
        return $this->loginName;
    }

    public function getRoles()
    {
        $stringRoles = [];

        /** @var Role $role */
        foreach ($this->roles as $role) {
            $stringRoles[] = is_string($role) ? $role : $role->getRole();
        }
        return $stringRoles;

    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return ArrayCollection
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * @param Booking $booking
     * @return User
     */
    public function addBooking(Booking $booking)
    {
        $this->bookings[] = $booking;
        return $this;
    }

    /**
     * @param Role $role
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        return $this;
    }
    
    /**
     * @param $roles[]
     * @return this
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param Booking $booking
     * @return bool
     */
    public function isUser(Booking $booking){
        return $booking->getUserId()->getId() === $this->getId();
    }

    /**
     * @return bool
     */
    public function isAdmin(){
        return in_array("ROLE_ADMIN", $this->getRoles());
    }
}