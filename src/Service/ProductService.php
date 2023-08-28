<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ProductService 
{
    private $logger;
    private $em;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em
    ) {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * @return type
     */
    public function getClassName()
    {
        return Product::class;
    }

    /**
     * Get all the products
     * 
     * @return array List of products
     */
    public function getallProducts(): array
    {
        return $this->em->getRepository(Product::class)->findAll();

    }

    /**
     * Get all the products by users
     * 
     * @return array List of users
     */
    public function getallProductsByUser(User $user): array
    {
        return $this->em->getRepository(Product::class)->findBy(['user' => $user]);
    }    
}