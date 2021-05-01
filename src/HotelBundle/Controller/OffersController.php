<?php

namespace HotelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OffersController extends Controller
{
    /**
     * @Route("/offers", name="view_offers")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewOffers(Request $request)
    {
        return $this->render('offers/view.html.twig');
    }
}
