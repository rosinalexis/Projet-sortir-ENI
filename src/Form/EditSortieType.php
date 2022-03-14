<?php

namespace App\Form;

use App\Entity\Sorties;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            //->add('date_debut')
            //->add('duree')
            //->add('date_cloture')
            //->add('nbInscriptionsMax')
            ->add('description')
            //->add('urlPhoto')
            //->add('organisateur')
            //->add('lieux')
            //->add('etats')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
