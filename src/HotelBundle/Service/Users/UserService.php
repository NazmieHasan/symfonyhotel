<?php

namespace HotelBundle\Service\Users;

use HotelBundle\Entity\User;
use HotelBundle\Repository\UserRepository;
use HotelBundle\Service\Encryption\ArgonEncryption;;
use HotelBundle\Service\Encryption\EncryptionServiceInterface;
use HotelBundle\Service\Roles\RoleService;
use HotelBundle\Service\Roles\RoleServiceInterface;
use Symfony\Component\Security\Core\Security;

class UserService implements UserServiceInterface
{

    private $security;
    private $userRepository;
    private $encryptionService;
    private $roleService;

    public function __construct(Security $security,
                                UserRepository $userRepository,
                                ArgonEncryption $encryptionService,
                                RoleServiceInterface $roleService)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->encryptionService = $encryptionService;
        $this->roleService = $roleService;
    }

    /**
     * @param string $email
     * @return User|null|object
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(User $user): bool
    {
        $passwordHash = $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);
        
        $userRole = $this->roleService->findOneBy("ROLE_USER");
        $user->addRole($userRole);

        return $this->userRepository->insert($user);
    }

    /**
     * @param int $id
     * @return User|null|object
     */
    public function findOneById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * @param User $user
     * @return User|null|object
     */
    public function findOne(User $user): ?User
    {
        return $this->userRepository->find($user);
    }

    /**
     * @return User|null|object
     */
    public function currentUser(): ?User
    {
        return $this->security->getUser();
    }

    public function update(User $user): bool
    {
        $oldPasswordHash = $user->getPassword();
        
        if ($user->getPassword()) {
            $newPasswordHash = $this->encryptionService->hash($user->getPassword());
            $user->setPassword($newPasswordHash);
        } else {
            $user->setPassword($oldPasswordHash); // not set $oldPasswordHash;
        }
            
        return $this->userRepository->update($user);
    }
    
    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(User $user): bool
    {
        return $this->userRepository->remove($user);
    }

    public function getAll()
    {
      return $this->userRepository->findBy([], ['id' => 'DESC']);
    }
    
    public function getCount()
    {
        return $this->userRepository->count([]);
    }
    
}
