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
                'label' => 'Votre Email addresse'
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
                'label' => 'Votre telephone',
                'attr' => [
                    'placeholder' => '06 12 34 56 78', // Sans indicatif
                    'class' => 'form-control' // Pour Bootstrap
                ]
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',
                ],
                'second_options' => [
                    'label' => 'Confirm Password',

                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.', // Message personnalisé ici
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
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
