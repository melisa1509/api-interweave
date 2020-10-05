<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\ProgramSa;

class ProgramSaType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
            ->add('mision1')
            ->add('mision2')
            ->add('mision3')
            ->add('mision4')

            ->add('generateGroups1')
            ->add('generateGroups2')
            ->add('generateGroups3')
            ->add('generateGroups4')
            ->add('generateGroups5')
            ->add('generateGroups6')
            ->add('generateGroups7')

            ->add('rule1')
            ->add('rule2')
            ->add('rule3')
            ->add('rule4')
            ->add('rule5')
            ->add('rule6')
            ->add('rule7')
            ->add('rule8')
            ->add('rule9')
            ->add('rule10')

            ->add('graduate1')
            ->add('graduate2')
            ->add('graduate3')
            ->add('graduate4')

            ->add('support1')
            ->add('support2')
            ->add('support3')
            ;
    
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => ProgramSa::class,
      'csrf_protection' => false
    ));
  }
}