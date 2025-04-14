<?php

namespace App\Form;

use App\Entity\Movilizacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovilizacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sedestacion')
            ->add('ayuda_deambulacion')
            ->add('ayuda_descripcion')
            ->add('cambios_posturales')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movilizacion::class,
        ]);
    }
}
