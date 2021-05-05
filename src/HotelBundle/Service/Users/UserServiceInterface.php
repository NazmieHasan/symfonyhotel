<?php

namespace HotelBundle\Service\Users;

use HotelBundle\Entity\User;

interface UserServiceInterface
{
    public function findOneByEmail(string $email) : ?User;
    public function save(User $user) : bool;
    public function update(User $user) : bool;
    public function delete(User $user) : bool;
    public function findOneById(int $id) : ?User;
    public function findOne(User $user) : ?User;
    public function currentUser() : ?User;
    public function getAll();

}