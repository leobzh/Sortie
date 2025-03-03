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
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de début',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes)',
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription',
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre maximum d\'inscriptions',
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Informations',
                'required' => false,
            ])
            ->add('urlPhoto', TextType::class, [
                'label' => 'URL de la photo',
                'required' => false,
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'label' => 'Lieu',
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class, // L'entité correspondante
                'label' => 'Je choisis l\'état de ma sortie :',
                'choices' => $this->etatRepository->findBy(['libelle' => ['CREATED', 'OPENED']]), // Filtrer les choix
                'choice_label' => function ($etat) {
                    return match ($etat->getLibelle()) {
                        'CREATED' => "J'enregistre en mode brouillon, je publierai plus tard",
                        'OPENED' => "J'enregistre ET je publie tout de suite !",
                        default => $etat->getLibelle(), // Au cas où d'autres états apparaissent
                    };
                },
                'expanded' => true, // Affiche sous forme de boutons radio
                'multiple' => false, // Une seule sélection possible
                'required' => true, // Champ obligatoire
                'data' => $this->etatRepository->findOneBy(['libelle' => 'CREATED']), // Définir 'CREATED' comme valeur par défaut
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'label' => 'Site',
            ])
            ->add('organisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom',
                'label' => 'Organisateur',
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