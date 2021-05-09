<?php

namespace HotelBundle\Service\Payments;

use HotelBundle\Entity\Payment;
use Doctrine\Common\Collections\ArrayCollection;

interface PaymentServiceInterface
{
    public function create(Payment $payment) : bool;
    public function edit(Payment $payment) : bool;
    public function delete(Payment $payment) : bool;
    public function getOne(int $id) : ?Payment;
    
    /**
     * @return ArrayCollection|Payment[]
     */
    public function getAll();
}