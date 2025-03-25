<?php

namespace App\Form;

use App\Entity\dieta;
use App\Entity\DietaHasTipoDieta;
use App\Entity\tipodieta;
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
                'class' => dieta::class,
                'choice_label' => 'id',
            ])
            ->add('tipo_dieta_id', EntityType::class, [
                'class' => tipodieta::class,
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
