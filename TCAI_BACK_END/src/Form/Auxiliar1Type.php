<?php

namespace App\Form;

use App\Entity\Auxiliar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Auxiliar1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('num_trabajador')
            ->add('nombre')
            ->add('apellidos')
            ->add('contraseña')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Auxiliar::class,
        ]);
    }
}
