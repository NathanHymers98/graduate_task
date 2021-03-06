<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findAllWithSearch(?string $term) // Queries the database for either successful or failed products based on what value is passed when this method is called
    {
        $qb = $this->createQueryBuilder('u');

        if ($term) {
            $qb->andWhere('u.username LIKE :term')
                ->setParameter('term', '%'.$term.'%');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findUsersId($id)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('u.id')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $qb->getResult();
    }

//    public function findAllUsersWithUnreadMessages($senderId)
//    {
//        $qb = $this->createQueryBuilder('u');
//        $qb
//            ->select('u.hasUnreadMessagesFrom')
//            ->andWhere('u.hasUnreadMessagesFrom LIKE :term')
//            ->setParameter('term', '%' . $senderId . '%');
//         return $qb->getQuery()
//            ->getResult();
//
//    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
