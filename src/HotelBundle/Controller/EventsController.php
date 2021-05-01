<?php

namespace HotelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventsController extends Controller
{
    /**
     * @Route("/events", name="view_events")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewEvents(Request $request)
    {
        return $this->render('events/view.html.twig');
    }
}
