<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Allergen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('description')
            ->add('composition')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'multiple' => false,
                'expanded' => true,

            ])
            ->add('image', ImageType::class)
            ->add('allergen', EntityType::class, [
                'class' => Allergen::class,
                'multiple' => true,
                'expanded' => true,

                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

}
class ApiProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')

            ->add('description')
            ->add('image', ImageType::class)
            ->add('allergen', EntityType::class, [
                'class' => Allergen::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function($allergen){
                    $test = $allergen->getImage();
                    return $test;
                }
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ApiProductType::class
        ));
    }
}
