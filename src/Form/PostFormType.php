<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('createdAt', null, [
                'widget' => 'single_text',
            ])*/
            ->add('text', TextareaType::class, [
                'attr' => array(
                    'class' => 'form-control mb-1',
                    'placeholder' => 'Dodaj opis',
                    'rows' => 3
                ),
                'label' => false,
            ])
            //->add('score')
            /*->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
