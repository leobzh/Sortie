<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [  // Ajout du type TextType::class
                'label' => 'Votre pseudo',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email (VOTRE_IDENTITE@campus-eni.fr obligatoire)'
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class, // L'entité Genre
                'choice_label' => 'nom', // Attribut de site à afficher
                'label' => 'Votre site de rattachement : ',
                'expanded' => true, // Transforme le champ en cases à cocher
                'required' => true, // Rend le champ obligatoire
            ])
            ->add('nom', TextType::class, [  // Ajout du type TextType::class
                'label' => 'Votre nom',
            ])
            ->add('prenom', TextType::class, [  // Ajout du type TextType::class
                'label' => 'Votre prénom',
            ])
            ->add('telephone', TelType::class, [  // Ajout du type TextType::class
                'required' => false, // Le champ n'est pas obligatoire
                'label' => 'Votre téléphone',
                'attr' => [
                    'placeholder' => '06 12 34 56 78', // Sans indicatif
                    'class' => 'form-control' // Pour Bootstrap
                ]
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Merci d\'accepter nos conditions d\'utilisation',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',

                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.', // Message personnalisé ici
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
