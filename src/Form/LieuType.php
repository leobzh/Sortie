<?php

namespace App\Form;

use App\Entity\Lieu;
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
                'label' => "Nom du lieu : ",
            ])
            // Champ pour l'autocomplétion (sous le champ nom)
            ->add('autocomplete', TextType::class, [
                'label' => "Adresse (auto-complétion) : ",
                'mapped' => false, // Ce champ n'est pas directement lié à l'entité
                'attr' => ['id' => 'lieu_autocomplete', 'placeholder' => 'Commencez à taper l\'adresse...'],
            ])
            ->add('rue', TextType::class, [

                'attr' => ['readonly' => true, 'hidden' => true], // Champ en lecture seule
            ])
            ->add('latitude', TextType::class, [

                'attr' => ['readonly' => true, 'hidden' => true], // Champ en lecture seule
            ])
            ->add('longitude', TextType::class, [

                'attr' => ['readonly' => true, 'hidden' => true], // Champ en lecture seule
            ])
            ->add('ville_nom', TextType::class, [

                'mapped' => false, // Ce champ n'est pas directement lié à l'entité
                'attr' => ['readonly' => true, 'hidden' => true], // Champ en lecture seule
            ])
            ->add('code_postal', TextType::class, [

                'mapped' => false, // Ce champ n'est pas directement lié à l'entité
                'attr' => ['readonly' => true, 'hidden' => true], // Champ en lecture seule
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
