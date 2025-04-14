<?php

namespace App\Form;

use App\Entity\ConstantesVitales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConstantesVitalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ta_sistolica')
            ->add('ta_diastolica')
            ->add('frecuencia_respiratoria')
            ->add('pulso')
            ->add('temperatura')
            ->add('saturacion_oxigeno')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ConstantesVitales::class,
        ]);
    }
}
