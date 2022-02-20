<?php

namespace HotelBundle\Controller;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController
{
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        if ($exception->getPrevious()->getCode() == 403 ) {
            return new Response("<p>Access Denied</p>");
        }
        
        return new Response("<h1>Something went wrong</h1>"); 
    }
    
}
