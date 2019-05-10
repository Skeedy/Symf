<?php

namespace App\Form\DataTransformer;

use App\Entity\Products;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProductToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (product) to a string (number).
     *
     * @param Product|null $product
     * @return string
     */
    public function transform($product)
    {
        if (null === $product) {
            return '';
        }

        return $product->getId();
    }

    /**
     * Transforms a string (number) to an object (product).
     *
     * @param string $productNumber
     * @return Product|null
     * @throws TransformationFailedException if object (product) is not found.
     */
    public function reverseTransform($productNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$productNumber) {
            return;
        }

        $product = $this->entityManager
            ->getRepository(Product::class)
            // query for the issue with this id
            ->find($productNumber);

        if (null === $productNumber) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'price with number "%s" does not exist!',
                $productNumber
            ));
        }

        return $product;
    }
}