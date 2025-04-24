<?php

namespace App\Form;

use App\Entity\DetalleDiagnostico;
use App\Entity\Diagnostico;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetalleDiagnosticoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('o2')
            ->add('o2_descripcion')
            ->add('panales')
            ->add('panales_descripcion')
            ->add('sv')
            ->add('sr')
            ->add('sng')
            ->add('avd')
            ->add('diagnostico_id', EntityType::class, [
                'class' => Diagnostico::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetalleDiagnostico::class,
        ]);
    }
}
