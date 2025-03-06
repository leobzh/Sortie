<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Repository\EtatRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\DateTime as DateTimeConstraint;

class SortieCreationType extends AbstractType
{
    private EtatRepository $etatRepository;

    public function __construct(EtatRepository $etatRepository)
    {
        $this->etatRepository = $etatRepository;
    }

    public function buildForm(FormBuilderInterface $builder, $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de la sortie est obligatoire.'])
                ]
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de début',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'constraints' => [
                    new NotBlank(['message' => 'La durée est obligatoire.']),
                    new Type(['type' => 'integer', 'message' => 'La durée doit être un nombre entier.'])
                ]
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription',
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre maximum d\'inscriptions',
                'constraints' => [
                    new NotBlank(['message' => 'Le nombre d\'inscriptions maximum est obligatoire.']),
                    new Type(['type' => 'integer', 'message' => 'Le nombre d\'inscriptions maximum doit être un nombre entier.'])
                ]
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Informations',
                'required' => false,
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'label' => 'Lieu',
                'constraints' => [
                    new NotBlank(['message' => 'Le lieu est obligatoire.'])
                ]
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
                'label' => 'Je choisis l\'état de ma sortie :',
                'choices' => $this->etatRepository->findBy(['libelle' => ['CREATED', 'OPENED']]),
                'choice_label' => function ($etat) {
                    return match ($etat->getLibelle()) {
                        'CREATED' => "Brouillon (ma sortie n'est pas publiée, elle n'est pas visible)",
                        'OPENED' => "Publiée (ma sortie est immédiatement visible)",
                        default => $etat->getLibelle(),
                    };
                },
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'data' => $this->etatRepository->findOneBy(['libelle' => 'CREATED']),
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Site',
                'constraints' => [
                    new NotBlank(['message' => 'Le site est obligatoire.'])
                ]
            ])
            ->add('organisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom',
                'label' => 'Organisateur',
                'constraints' => [
                    new NotBlank(['message' => 'L\'organisateur est obligatoire.'])
                ]
            ])
            ->add('participants', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Participants',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'csrf_protection' => true,
        ]);
    }
}
