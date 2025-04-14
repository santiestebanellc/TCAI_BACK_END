<?php

namespace App\Form;

use App\Entity\Dieta;
use App\Entity\TipoTextura;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DietaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('autonomo')
            ->add('protesi')
            ->add('tipo_textura_id', EntityType::class, [
                'class' => TipoTextura::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dieta::class,
        ]);
    }
}
