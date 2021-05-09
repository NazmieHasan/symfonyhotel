<?php

namespace HotelBundle\Service\Categories;


use HotelBundle\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;

interface CategoryServiceInterface
{  
    public function create(Category $category) : bool;
    public function edit(Category $category) : bool;
    public function delete(Category $category) : bool;
    public function getOne(int $id) : ?Category;
    
    /**
     * @return ArrayCollection|Category[]
     */
    public function getAll();
}