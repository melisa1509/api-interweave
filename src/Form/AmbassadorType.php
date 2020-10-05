<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class AmbassadorType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('username')
      ->add('password')
      ->add('language')
      ->add('firstName')
      ->add('lastName')
      ->add('country')
      ->add('city')
      ->add('whatsapp')
      ->add('code')
     
      
    ;
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => User::class,
      'csrf_protection' => false
    ));
  }
}