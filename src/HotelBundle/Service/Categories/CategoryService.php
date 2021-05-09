<?php

namespace HotelBundle\Service\Categories;

use HotelBundle\Entity\Category;
use HotelBundle\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;

class CategoryService implements CategoryServiceInterface
{

    private $categoryRepository;

    /**
     * CategoryService constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Category $category
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Category $category): bool
    {
        return $this->categoryRepository->insert($category);
    }

    /**
     * @param Category $category
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Category $category): bool
    {
        return $this->categoryRepository->update($category);
    }

    /**
     * @param Category $category
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Category $category): bool
    {
        return $this->categoryRepository->remove($category);
    }
    
    /**
     * @param int $id
     * @return Category|null|object
     */
    public function getOne(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * @return ArrayCollection[]
     */
    public function getAll()
    {
        return $this->categoryRepository->findAll();
    }
 
}