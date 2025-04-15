<?php

namespace App\Form;

use App\Entity\Auxiliar;
use App\Entity\Diagnostico;
use App\Entity\Paciente;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiagnosticoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('diagnostico')
            ->add('motivo')
            ->add('fecha', null, [
                'widget' => 'single_text',
            ])
            ->add('toma')
            ->add('paciente_id', EntityType::class, [
                'class' => Paciente::class,
                'choice_label' => 'id',
            ])
            ->add('auxiliar_id', EntityType::class, [
                'class' => Auxiliar::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Diagnostico::class,
        ]);
    }
}
