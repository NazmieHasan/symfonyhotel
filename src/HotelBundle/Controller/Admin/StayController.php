<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Stay;
use HotelBundle\Form\StayType;
use HotelBundle\Service\Stays\StayServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/stays")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class StayController extends Controller
{
    /**
     * @var StayServiceInterface
     */
    private $stayService;
    
    /**
     * StayController constructor.
     * @param StayServiceInterface $stayService
     */
    public function __construct(
        StayServiceInterface $stayService)
    {
        $this->stayService = $stayService;
    }
    
    /**
     * @Route("/create", name="admin_stay_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('admin/stays/create.html.twig',
            ['form' => $this
                ->createForm(StayType::class)
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
        $stay = new Stay();
        $form = $this->createForm(StayType::class, $stay);
        $form->handleRequest($request);
        
        $this->stayService->create($stay);
        $this->addFlash("info", "Create stay successfully!");
        return $this->redirectToRoute("admin_stays");
    }
    
    /**
     * @Route("/", name="admin_stays")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllStays()
    {
        $stays = $this->stayService->getAll();

        return $this->render("admin/stays/list.html.twig",
            [
                'stays' => $stays
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_stay_edit", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $stay = $this->stayService->getOne($id);
        
        if (null === $stay){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/stays/edit.html.twig',
            [
                'form' => $this->createForm(StayType::class)
                       ->createView(),
                'stay' => $stay
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
        $stay = $this->stayService->getOne($id);
        
        $form = $this->createForm(StayType::class, $stay);
        $form->handleRequest($request);
        
        $this->stayService->edit($stay);

        return $this->redirectToRoute("admin_stays");
    }
    
    /**
     * @Route("/delete/{id}", name="admin_stay_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $stay = $this->stayService->getOne($id);

        $form = $this->createForm(StayType::class, $stay);
        $form->handleRequest($request);
        $this->stayService->delete($stay);
        $this->addFlash("info", "Stay is deleted");
        
        return $this->redirectToRoute("admin_stays");
    }
    
}