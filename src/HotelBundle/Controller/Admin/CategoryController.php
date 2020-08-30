<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Category;
use HotelBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/create", name="admin_category_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/categories/create.html.twig',
            ['form' => $form->createView()]);
    }

    /**
     * @Route("/", name="admin_categories")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategories(){
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('admin/categories/list.html.twig', ['categories' => $categories]);
    }

}
