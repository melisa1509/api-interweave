<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Evaluation;

class EvaluationPreType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    ->add('question1')
    ->add('question2')
    ->add('question3')
    ->add('question4')
    ->add('question5')
    ->add('question6')
    ->add('question7')   
    ;
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => Evaluation::class,
      'csrf_protection' => false
    ));
  }
}