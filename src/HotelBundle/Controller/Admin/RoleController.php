<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Role;
use HotelBundle\Form\RoleType;
use HotelBundle\Service\Roles\RoleServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/roles")
 * Class RoleController
 * @package HotelBundle\Controller\Admin
 */
class RoleController extends Controller
{
    /**
     * @var RoleServiceInterface
     */
    private $roleService;
    
    /**
     * RoleController constructor.
     * @param RoleServiceInterface $roleService
     */
    public function __construct(
        RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }
    
    /**
     * @Route("/create", name="admin_role_create", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('admin/roles/create.html.twig',
            ['form' => $this
                ->createForm(RoleType::class)
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
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);
        
        $this->roleService->create($role);
        $this->addFlash("info", "Create role successfully!");
        return $this->redirectToRoute("admin_roles");
    }
    
    /**
     * @Route("/", name="admin_roles")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllRoles(Request $request)
    {
        $roles = $this->roleService->getAll();

        return $this->render("admin/roles/list.html.twig",
            [
                'roles' => $roles
            ]);
    }
    
}
