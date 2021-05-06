<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Category;
use HotelBundle\Form\CategoryType;
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
            
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/categories/create.html.twig',
            ['form' => $form->createView()]);
    }

    /**
     * @Route("/", name="admin_categories")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategories(){
        $categories = $this
            ->getDoctrine()
            ->getRepository(Category::class)->findAll();
        return $this->render('admin/categories/list.html.twig',
            ['categories' => $categories]);
    }

    /**
     * @Route("/edit/{id}", name="admin_category_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id, Request $request)
    {
        $category = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);


        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            
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
            
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("admin_categories");
        }

        return $this->render('admin/categories/edit.html.twig',
            ['category' => $category, 'form' => $form->createView()]);
    }




    /**
     * @Route("/delete/{id}", name="admin_category_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id, Request $request)
    {
        $category = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->remove('image');
        $form->handleRequest($request);


        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            return $this->redirectToRoute("admin_categories");
        }

        return $this->render('admin/categories/delete.html.twig',
            ['category' => $category, 'form' => $form->createView()]);
    }

}
