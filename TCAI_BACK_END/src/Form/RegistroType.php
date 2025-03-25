<?php

namespace App\Form;

use App\Entity\auxiliar;
use App\Entity\constantesvitales;
use App\Entity\diagnostico;
use App\Entity\dieta;
use App\Entity\drenaje;
use App\Entity\movilizacion;
use App\Entity\paciente;
use App\Entity\Registro;
use App\Entity\tipohigiene;
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
                'widget' => 'single_text',
            ])
            ->add('observaciones')
            ->add('auxiliar_id', EntityType::class, [
                'class' => auxiliar::class,
                'choice_label' => 'id',
            ])
            ->add('paciente_id', EntityType::class, [
                'class' => paciente::class,
                'choice_label' => 'id',
            ])
            ->add('tipo_higiene_id', EntityType::class, [
                'class' => tipohigiene::class,
                'choice_label' => 'id',
            ])
            ->add('dieta_id', EntityType::class, [
                'class' => dieta::class,
                'choice_label' => 'id',
            ])
            ->add('drenaje_id', EntityType::class, [
                'class' => drenaje::class,
                'choice_label' => 'id',
            ])
            ->add('movilizacion_id', EntityType::class, [
                'class' => movilizacion::class,
                'choice_label' => 'id',
            ])
            ->add('diagnostico_id', EntityType::class, [
                'class' => diagnostico::class,
                'choice_label' => 'id',
            ])
            ->add('constantes_vitales_id', EntityType::class, [
                'class' => constantesvitales::class,
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
