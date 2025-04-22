<?php

namespace App\Form;

use App\Entity\BalanceHidrico;
use App\Entity\Higiene;
use App\Entity\Registro;
use App\Entity\Sueroterapia;
use App\Entity\Auxiliar;
use App\Entity\ConstantesVitales;
use App\Entity\Dieta;
use App\Entity\Drenaje;
use App\Entity\Movilizacion;
use App\Entity\Observacion;
use App\Entity\Paciente;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fecha', null, [
                'widget' => 'single_text'
            ])
            ->add('toma')
            ->add('auxiliar_id', EntityType::class, [
                'class' => Auxiliar::class,
                'choice_label' => 'id',
            ])
            ->add('paciente_id', EntityType::class, [
                'class' => Paciente::class,
                'choice_label' => 'id',
            ])
            ->add('observacion_id', EntityType::class, [
                'class' => Observacion::class,
                'choice_label' => 'id',
            ])
            ->add('dieta_id', EntityType::class, [
                'class' => Dieta::class,
                'choice_label' => 'id',
            ])
            ->add('drenaje_id', EntityType::class, [
                'class' => Drenaje::class,
                'choice_label' => 'id',
            ])
            ->add('movilizacion_id', EntityType::class, [
                'class' => Movilizacion::class,
                'choice_label' => 'id',
            ])
            ->add('constantes_vitales_id', EntityType::class, [
                'class' => ConstantesVitales::class,
                'choice_label' => 'id',
            ])
            ->add('balance_hidrico_id', EntityType::class, [
                'class' => BalanceHidrico::class,
                'choice_label' => 'id',
            ])
            ->add('sueroterapia_id', EntityType::class, [
                'class' => Sueroterapia::class,
                'choice_label' => 'id',
            ])
            ->add('higiene_id', EntityType::class, [
                'class' => Higiene::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registro::class,
        ]);
    }
}
