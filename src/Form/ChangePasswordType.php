<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
            ])
            ->add('new_password', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmer le nouveau mot de passe',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Changer le mot de passe',
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
