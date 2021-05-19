<?php

namespace HotelBundle\Controller\Admin;

use HotelBundle\Entity\Payment;
use HotelBundle\Form\PaymentType;
use HotelBundle\Service\Payments\PaymentServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/payments")
 * Class CategoryController
 * @package HotelBundle\Controller\Admin
 */
class PaymentController extends Controller
{
    /**
     * @var PaymentServiceInterface
     */
    private $paymentService;
    
    /**
     * PaymentController constructor.
     * @param PaymentServiceInterface $paymentService
     */
    public function __construct(
        PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    /**
     * @Route("/create", name="admin_payment_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return $this->render('admin/payments/create.html.twig',
            ['form' => $this
                ->createForm(PaymentType::class)
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
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        
        $this->paymentService->create($payment);
        $this->addFlash("info", "Create payment successfully!");
        return $this->redirectToRoute("admin_payments");
    }
    
     /**
     * @Route("/", name="admin_payments")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllPayments()
    {
        $payments = $this->paymentService->getAll();

        return $this->render("admin/payments/list.html.twig",
            [
                'payments' => $payments
            ]);
    }
    
    /**
     * @Route("/edit/{id}", name="admin_payment_edit", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id)
    {
        $payment = $this->paymentService->getOne($id);
        
        if (null === $payment){
            return $this->redirectToRoute("hotel_index");
        }

        return $this->render('admin/payments/edit.html.twig',
            [
                'form' => $this->createForm(PaymentType::class)
                       ->createView(),
                'payment' => $payment
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
        $payment = $this->paymentService->getOne($id);
        
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        
        $this->paymentService->edit($payment);

        return $this->redirectToRoute("admin_payments");
    }
    
    /**
     * @Route("/delete/{id}", name="admin_payment_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $payment = $this->paymentService->getOne($id);

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        $this->paymentService->delete($payment);
        $this->addFlash("info", "Payment is deleted");
        
        return $this->redirectToRoute("admin_payments");
    }
    
}