<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\ProgramMbs;

class ProgramMbsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
              ->add('plan1')
              ->add('plan2')
              ->add('product1')
              ->add('product2')
              ->add('product3')
              ->add('product4')
              ->add('product5')
              ->add('product6')
              ->add('product7')
              ->add('process1')
              ->add('process2')
              ->add('process3')
              ->add('process4')
              ->add('price1')
              ->add('price2')
              ->add('price3')
              ->add('price4')
              ->add('paperwork1')
              ->add('paperwork2')
              ->add('paperwork3')
              ->add('paperwork4')
              ->add('paperwork5')
              ->add('paperwork6')
              ->add('paperwork7')
              ->add('paperwork8')
              ->add('process1')
              ->add('process2')
              ->add('process3')
              ->add('process4')
              ->add('promotion1')
              ->add('promotion2')
              ->add('promotion3')
              ->add('promotion4')
              ->add('promotion5')              
              ->add('quality_p1')
              ->add('quality_p2')
              ->add('quality_p3')
              ->add('quality_p4')
              ->add('quality_p5')
              ->add('quality_p6')
              ->add('quality_p7')
              ->add('quality_p8')
              ->add('quality_q1')
              ->add('quality_q2')
              ->add('quality_q3')
              ->add('quality_q4')
              ->add('quality_q5')
              ->add('quality_q6')
              ->add('quality_q7')
              ->add('quality_q8')
              ->add('quality_g1')
              ->add('quality_g2')
              ->add('quality_g3')
              ->add('quality_g4')
              ->add('quality_g5')
              ->add('quality_g6')
              ->add('quality_g7')
              ->add('qualityg8')
              ->add('service1')
              ->add('service2')
              ->add('service3')
              ->add('service4')
              ->add('service5')
              ->add('service6')
              ->add('history1')
              ->add('history2')
              ;
    
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => ProgramMbs::class,
      'csrf_protection' => false
    ));
  }
}