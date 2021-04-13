<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\GrantAmbassador;

class GrantAmbassadorType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    ->add('code')
    ->add('number')
    ->add('file')
    ->add('file2')
    ->add('file3')
    ->add('file2')
    ->add('file2')
    ->add('file2')
    ->add('file2')
    ->add('question1')
    ->add('question2')
    ->add('question3')
    ->add('question4')
    ->add('question5')
    ->add('question6')
    ->add('question7')
    ->add('question8')
    ->add('question9')
    ->add('question10')
    ->add('question11')
    ->add('question12')
    ;
     
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => GrantAmbassador::class,
      'csrf_protection' => false
    ));
  }
}