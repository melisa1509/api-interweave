<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Groupe;

class GroupType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
    ->add('name')
    ->add('interweaveLocal')
    ->add('authorizationCode')
    ->add('modality')
    ->add('program')
    ->add('name_image')
    ->add('numberStudents')
    ->add('number_students_graduated')
    ->add('authorizationCode')
    ->add('interweaveLocal')
    ;
     
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => Groupe::class,
      'csrf_protection' => false
    ));
  }
}