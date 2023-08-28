<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getProductWithCategory(User $user = null): array
    {
        if($user){
            return $this
                ->createQueryBuilder('p')
                ->select('p as item, count(p.id) as total')
                ->where('p.user = :user')
                ->setParameter('user', $user)
                ->groupBy('p.category')
                ->getQuery()
                ->getResult();
        } else {
            return $this
                ->createQueryBuilder('p')
                ->select('p as item, count(p.id) as total')
                ->groupBy('p.category')
                ->getQuery()
                ->getResult();
        }
    }

   
}
