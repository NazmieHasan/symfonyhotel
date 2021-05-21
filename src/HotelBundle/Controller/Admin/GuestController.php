<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Guest;
use HotelBundle\Entity\Stay;
use HotelBundle\Form\GuestType;
use HotelBundle\Service\Guests\GuestServiceInterface;
use HotelBundle\Service\Stays\StayServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/guests")
 * Class GuestController
 * @package HotelBundle\Controller\Admin
 */
class GuestController extends Controller
{
    /**
     * @var GuestServiceInterface
     */
    private $guestService;
    
    /**
     * @var StayServiceInterface
     */
    private $stayService;
    
    /**
     * GuestController constructor.
     * @param GuestServiceInterface $guestService
     * @param StayServiceInterface $stayService
     */
    public function __construct(
        GuestServiceInterface $guestService,
        StayServiceInterface $stayService)
    {
        $this->guestService = $guestService;
        $this->stayService = $stayService;
    }
    
    /**
     * @Route("/create", name="admin_guest_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('admin/guests/create.html.twig',
            ['form' => $this
                ->createForm(GuestType::class)
                ->createView()]);
    }
    
    /**
     * @Route("/create", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request)
    {
        $guest = new Guest();
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);
        
        $this->guestService->create($guest);
        $this->addFlash("info", "Create guest successfully!");
        return $this->redirectToRoute("admin_guests");
    }
    
    /**
     * @Route("/view/{id}", name="admin_guest_view")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(int $id) {
        $guest = $this->guestService->getOne($id);
        
        $stays = $this->stayService->getAllByGuestId($id);

        return $this->render("admin/guests/view.html.twig",
            [
                'guest' => $guest,
                'stays' => $stays,
            ]);
    }
    
    /**
     * @Route("/", name="admin_guests")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllGuests(Request $request)
    {
        $guests = $this->guestService->getAll();
        
        if($request->isMethod("POST")) {  
            $personalNumber = $request->get('personalNumber');
        
            $em = $this->getDoctrine()->getManager();
            $guests = $em->getRepository("HotelBundle:Guest")
                       ->findBy(
                           [
                               'personalNumber' => $personalNumber
                           ]);
            }

        return $this->render("admin/guests/list.html.twig",
            [
                'guests' => $guests
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_guest_edit", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $guest = $this->guestService->getOne($id);
        
        if (null === $guest){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/guests/edit.html.twig',
            [
                'form' => $this->createForm(GuestType::class)
                       ->createView(),
                'guest' => $guest
            ]);

    }
    
    /**
     * @Route("/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Responsen
     */
    public function editProcess(Request $request, int $id)
    {
        $guest = $this->guestService->getOne($id);
        
        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);
        
        $this->guestService->edit($guest);

        return $this->redirectToRoute("admin_guests");
    }
    
    /**
     * @Route("/delete/{id}", name="admin_guest_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $guest = $this->guestService->getOne($id);

        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);
        $this->guestService->delete($guest);
        $this->addFlash("info", "Guest is deleted");
        
        return $this->redirectToRoute("admin_guests");
    }
    
}