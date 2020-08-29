<?php

namespace HotelBundle\Controller;

use AppBundle\Service\Bookings\BookingServiceInterface;
use AppBundle\Service\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var BookingServiceInterface\
     */
    private $articleService;

    public function __construct(
        UserServiceInterface $userService,
        BookingServiceInterface $bookingService)
    {
        $this->userService = $userService;
        $this->bookingService = $bookingService;
    }

    /**
     * @Route("/admin/index", name="admin_index")
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/bookings", name="admin_bookings", methods={"GET"})
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function bookingsAction(){
        return $this->render('admin/bookings.html.twig',
            [
                'bookings' => $this->bookingService->getAll()

            ]);
    }

    /**
     * @Route("/admin/users", name="admin_users", methods={"GET"})
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function usersAction()
    {
        return $this->render('admin/users.html.twig',
            [
                'users' => $this->userService->getAll()
            ]);
    }

}
