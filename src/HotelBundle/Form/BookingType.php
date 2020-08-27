<?php

namespace HotelBundle\Form;

use HotelBundle\Entity\Category;
use HotelBundle\Entity\Payment;
use HotelBundle\Entity\Room;
use HotelBundle\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BookingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class,
                ['class' => Category::class])
            ->add('checkin', DateType::class, array(
                "widget" => 'single_text',
                "format" => 'yyyy-MM-dd',
                "data" => new \DateTime()
            ))
            ->add('checkout', DateType::class, array(
                "widget" => 'single_text',
                "format" => 'yyyy-MM-dd',
                "data" => new \DateTime()
            ))
            ->add('days', NumberType::class)
            ->add('adults', NumberType::class)
            ->add('childBed', NumberType::class)
            ->add('totalAmount', NumberType::class)
            ->add('payment', EntityType::class,
                ['class' => Payment::class])
            ->add('paidAmount', NumberType::class)
            ->add('paymentAmount', NumberType::class)
            ->add('status', EntityType::class,
                ['class' => Status::class])
            ->add('room', EntityType::class,
                ['class' => Room::class]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HotelBundle\Entity\Booking'
        ));
    }


}
