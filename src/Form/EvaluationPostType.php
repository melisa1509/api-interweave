<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Evaluation;

class EvaluationPostType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    ->add('postquestion1')
    ->add('postquestion2')
    ->add('postquestion3')
    ->add('postquestion4')
    ->add('postquestion5')
    ->add('postquestion6')
    ->add('postquestion7')
    ->add('postquestion8')
    ->add('postquestion9')
    ->add('postquestion10');
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