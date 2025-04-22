<?php

namespace App\Form;

use App\Entity\DietaHasTipoDieta;
use App\Entity\Dieta;
use App\Entity\TipoDieta;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DietaHasTipoDietaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dieta_id', EntityType::class, [
                'class' => Dieta::class,
                'choice_label' => 'id',
            ])
            ->add('tipo_dieta_id', EntityType::class, [
                'class' => TipoDieta::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DietaHasTipoDieta::class,
        ]);
    }
}
