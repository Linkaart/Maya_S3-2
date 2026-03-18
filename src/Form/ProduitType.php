<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé'
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'invalid_message' => 'Nombre attendu'
            ])
//            ->add('dateCreation')      // date de création non saisie car positionnée à date du jour dans le constructeur Produit
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('cru')
            ->add('cuit')
            ->add('bio')
            ->add('debutDisponibilite', DateType::class,  [
                'label' => 'Début disponibilité',
                'widget' => 'single_text',
            ])
            ->add('finDisponibilite',DateType::class, [
                'label' => 'Fin disponibilité',
                'widget' => 'single_text',
            ])
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Categorie::class,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false
            ])
//            ->add('recettes')    // on ne gère pas les recettes dans la gestion des produits
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
