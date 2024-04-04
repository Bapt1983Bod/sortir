<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Webmozart\Assert\Tests\StaticAnalysis\minLength;

class CreationSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', null, [
                'widget' => 'single_text',
                'attr'   => ['min' => ( new \DateTime() )->format('Y-m-d H:i')]
            ])
            ->add('duree',null,[
                'label' => 'Duree en minutes',
                'attr' => ['min' => 15 , 'max' => 180],

            ])
            ->add('dateLimiteInscription', null, [
                'widget' => 'single_text',
                'attr'   => ['min' => ( new \DateTime('tomorrow') )->format('Y-m-d'),
                'message' =>'La date limite d\'inscription ne peux pas être avant la date de début']
            ])
            ->add('nbInscriptionsmax',null ,[
                'attr' => ['min' => 1, 'max' => 50]]
            )
            ->add('infosSortie', null, [
                'attr' => ['minLength' => 10, 'maxlength' => 500]
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
