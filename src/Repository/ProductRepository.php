<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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


    public function findAllWithSearch(?string $term, $isSuccessful) // Queries the database for either successful or failed products based on what value is passed when this method is called
    {
        $qb = $this->createQueryBuilder('p');

        if ($isSuccessful == true) {
            $qb->andWhere('p.isSuccessful = true');
        } else {
            if ($isSuccessful == false) {
                $qb->andWhere('p.isSuccessful = false');
            }
        }
            if ($term) {
                $qb->andWhere('p.productCode LIKE :term OR p.productName LIKE :term OR p.productDescription LIKE :term OR p.productStock LIKE :term OR p.netCost LIKE :term OR p.isDiscontinued LIKE :term OR p.reasonsForFailure LIKE :term')
                    ->setParameter('term', '%' . $term . '%');
            }
        return $qb
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
