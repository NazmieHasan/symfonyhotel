<?php

namespace HotelBundle\Controller;

use HotelBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="hotel_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAll();
        // replace this example code with whatever you need
        return $this->render('home/index.html.twig',
        ['categories' => $categories]);
    }
}
