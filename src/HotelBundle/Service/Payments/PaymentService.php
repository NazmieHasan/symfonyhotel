<?php

namespace HotelBundle\Service\Payments;

use HotelBundle\Entity\Payment;
use HotelBundle\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;

class PaymentService implements PaymentServiceInterface
{

    private $paymentRepository;

    /**
     * PaymentService constructor.
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param Payment $payment
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Payment $payment): bool
    {
        return $this->paymentRepository->insert($payment);
    }
    
    /**
     * @param Payment $payment
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Payment $payment): bool
    {
        return $this->paymentRepository->update($payment);
    }

    /**
     * @param Payment $payment
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Payment $payment): bool
    {
        return $this->paymentRepository->remove($payment);
    }
    
    /**
     * @param int $id
     * @return Payment|null|object
     */
    public function getOne(int $id): ?Payment
    {
        return $this->paymentRepository->find($id);
    }

    /**
     * @return ArrayCollection[]
     */
    public function getAll()
    {
        return $this->paymentRepository->findBy([], ['id' => 'ASC']);
    }
       
}