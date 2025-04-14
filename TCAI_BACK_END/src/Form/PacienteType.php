<?php

namespace App\Form;

use App\Entity\Paciente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PacienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('num_historial')
            ->add('nombre')
            ->add('apellidos')
            ->add('fecha_nacimiento', null, [
                'widget' => 'single_text'
            ])
            ->add('direccion_completa')
            ->add('lengua_materna')
            ->add('antecedentes')
            ->add('alergias')
            ->add('nombre_cuidador')
            ->add('telefono_cuidador')
            ->add('fecha_ingreso', null, [
                'widget' => 'single_text'
            ])
            ->add('timestamp', null, [
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paciente::class,
        ]);
    }
}
