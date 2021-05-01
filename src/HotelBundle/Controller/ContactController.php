<?php

namespace HotelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="view_contact")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewContact(Request $request)
    {
        return $this->render('contact/view.html.twig');
    }
}
