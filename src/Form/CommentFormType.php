<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('createdAt', null, [
                'widget' => 'single_text',
            ])*/
            ->add('text', TextareaType::class, [
                'attr' => array(
                    'class' => 'form-control bg-dark text-white color-white my-1',
                    'rows' => 3,
                ),
                'label' => false,
            ])
            /*->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])*/
            /*->add('post', EntityType::class, [
                'class' => Post::class,
                'choice_label' => 'id',
            ])*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
