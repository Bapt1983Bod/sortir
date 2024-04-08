<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[

                    'attr' => ['class'=> 'form-control']

            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'required'=>false,
                'type' => PasswordType::class,
                'options' => [
                    'attr' => ['autocomplete' => 'new-password',
                        'class'=> 'form-control'],
                ],
                'first_options' => [
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => "Confirmez le mot de passe"
                ],
                'invalid_message' => 'Le mot de passe ne correspond pas à sa confirmation'
            ])
            ->add('nom', TextType::class ,[
                'attr' => ['class'=> 'form-control']
            ])
            ->add('prenom', TextType::class ,[
                'attr' => ['class'=> 'form-control']
            ])
            ->add('telephone', TextType::class ,[
                'attr' => ['class'=> 'form-control']
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'attr' => ['class'=> 'form-control']
            ])
            ->add('image_file', FileType::class,[
                'mapped'=>false,
                'required'=>false,
                'attr' => ['class'=> 'form-control'],
                'constraints' =>[
                    new Image([
                        'mimeTypes'=>[
                            'image/png',
                            'image/jpg',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage'=>"Ce format n'est pas accepté",
                        'maxSize'=>'1024k',
                        'maxSizeMessage'=>"la taille de l'image est limitée à 1Mo"
                    ])
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
