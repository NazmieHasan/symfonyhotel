<?php

namespace HotelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RelaxZoneController extends Controller
{
    /**
     * @Route("/relax-zone", name="view_relax_zone")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewRelaxZone(Request $request)
    {
        return $this->render('relax-zone/view.html.twig');
    }
}
