<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Category;
use HotelBundle\Form\CategoryType;
use HotelBundle\Entity\Room;
use HotelBundle\Form\RoomType;
use HotelBundle\Service\Categories\CategoryServiceInterface;
use HotelBundle\Service\Rooms\RoomServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/categories")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class CategoryController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;
    
    /**
     * @var RoomServiceInterface
     */
    private $roomService;
    
     /**
     * CategoryController constructor.
     * @param CategoryServiceInterface $categoryService
     * @param RoomServiceInterface $roomService
     */
    public function __construct(
        CategoryServiceInterface $categoryService,
        RoomServiceInterface $roomService)
    {
        $this->categoryService = $categoryService;
        $this->roomService = $roomService;
    }
    
    /**
     * @Route("/create", name="admin_category_create", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('admin/categories/create.html.twig',
            ['form' => $this
                ->createForm(CategoryType::class)
                ->createView()]);
    }
    
    /**
     * @Route("/create", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request)
    {
        $category = new category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        /** @var UploadedFile $file */
            $file = $form['image']->getData();

            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            if ($file) {
                $file->move(
                    $this->getParameter('categories_directory'),
                    $fileName
                );
            $category->setImage($fileName);
            }
        
        $this->categoryService->create($category);
        $this->addFlash("info", "Create category successfully!");
        return $this->redirectToRoute("admin_categories");
    }

    
    /**
     * @Route("/view/{id}", name="admin_category_view")
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) {
        $category = $this->categoryService->getOne($id);
        
        $rooms = $this->roomService->getAllByCategoryId($id);

        return $this->render("admin/categories/view.html.twig",
            [
                'category' => $category,
                'rooms' => $rooms
            ]);
    }

    /**
     * @Route("/", name="admin_categories")
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllCategories()
    {
        $categories = $this->categoryService->getAll();
        
        return $this->render("admin/categories/list.html.twig",
            [
                'categories' => $categories
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_category_edit", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $category = $this->categoryService->getOne($id);
        
        if (null === $category){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/categories/edit.html.twig',
            [
                'form' => $this->createForm(CategoryType::class)
                       ->createView(),
                'category' => $category
            ]);

    }
    
    /**
     * @Route("/edit/{id}", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Responsen
     */
    public function editProcess(Request $request, int $id)
    {
        $category = $this->categoryService->getOne($id);
        
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        /** @var UploadedFile $file */
            $file = $form['image']->getData();

            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            if ($file) {
                $file->move(
                    $this->getParameter('categories_directory'),
                    $fileName
                );
            $category->setImage($fileName);
            }
        
        $this->categoryService->edit($category);

        return $this->redirectToRoute("admin_categories");
    }
    
    /**
     * @Route("/delete/{id}", name="admin_category_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $category = $this->categoryService->getOne($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->remove('image');
        $form->handleRequest($request);
        $this->categoryService->delete($category);
        $this->addFlash("info", "Category is deleted");
        
        return $this->redirectToRoute("admin_categories");
    }
    
}
