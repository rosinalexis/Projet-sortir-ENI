<?php

namespace App\Form;

use App\Entity\Villes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_ville', null, ["label" => "Nom ville*:"])
            ->add('code_postal', null, ["label" => "Code postal*:"]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Villes::class,
        ]);
    }
}
