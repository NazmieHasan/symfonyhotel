<?php

namespace HotelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VenuesController extends Controller
{
    /**
     * @Route("/venues", name="view_venues")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewVenues(Request $request)
    {
        return $this->render('venues/view.html.twig');
    }
}
