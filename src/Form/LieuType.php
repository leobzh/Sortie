<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Donnez un nom à votre lieu : ",
            ])
            ->add('rue', TextType::class, [
                'label' => "Rue : ",
                'attr' => ['id' => 'adresse'], // Ajout d'un ID pour cibler l'input avec JavaScript
            ])
            ->add('latitude', TextType::class, [
                'label' => "Latitude : ",
                'attr' => ['id' => 'latitude'], // ID pour lier au JavaScript
            ])
            ->add('longitude', TextType::class, [
                'label' => "Longitude : ",
                'attr' => ['id' => 'longitude'], // ID pour lier au JavaScript
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => function (Ville $ville) {
                    return $ville->getNom() . ' (' . $ville->getCodePostal() . ')';
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
