<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Status;
use HotelBundle\Form\StatusType;
use HotelBundle\Service\Statuses\StatusServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/statuses")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class StatusController extends Controller
{
    /**
     * @var StatusServiceInterface
     */
    private $statusService;
    
    /**
     * StatusController constructor.
     * @param StatusServiceInterface $statusService
     */
    public function __construct(
        StatusServiceInterface $statusService)
    {
        $this->statusService = $statusService;
    }
    
    /**
     * @Route("/create", name="admin_status_create", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('admin/statuses/create.html.twig',
            ['form' => $this
                ->createForm(StatusType::class)
                ->createView()]);
    }
    
    /**
     * @Route("/create", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request)
    {
        $status = new Status();
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);
        
        $this->statusService->create($status);
        $this->addFlash("info", "Create status successfully!");
        return $this->redirectToRoute("admin_statuses");
    }
    
    /**
     * @Route("/", name="admin_statuses")
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllStatuses()
    {
        $statuses = $this->statusService->getAll();

        return $this->render("admin/statuses/list.html.twig",
            [
                'statuses' => $statuses
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_status_edit", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $status = $this->statusService->getOne($id);
        
        if (null === $status){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/statuses/edit.html.twig',
            [
                'form' => $this->createForm(StatusType::class)
                       ->createView(),
                'status' => $status
            ]);

    }
    
    /**
     * @Route("/edit/{id}", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Responsen
     */
    public function editProcess(Request $request, int $id)
    {
        $status = $this->statusService->getOne($id);
        
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);
        
        $this->statusService->edit($status);

        return $this->redirectToRoute("admin_statuses");
    }
   
    /**
     * @Route("/delete/{id}", name="admin_status_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $status = $this->statusService->getOne($id);

        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);
        $this->statusService->delete($status);
        $this->addFlash("info", "Status is deleted");
        
        return $this->redirectToRoute("admin_statuses");
    }
    
}
