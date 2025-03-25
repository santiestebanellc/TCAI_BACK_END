<?php

namespace App\Form;

use App\Entity\habitacion;
use App\Entity\paciente;
use App\Entity\PacienteHasHabitaciones;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PacienteHasHabitacionesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timestamp', null, [
                'widget' => 'single_text',
            ])
            ->add('paciente_id', EntityType::class, [
                'class' => paciente::class,
                'choice_label' => 'id',
            ])
            ->add('habitacion_id', EntityType::class, [
                'class' => habitacion::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PacienteHasHabitaciones::class,
        ]);
    }
}
