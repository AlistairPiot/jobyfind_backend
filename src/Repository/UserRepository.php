<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Trouve les utilisateurs par rôle
     * @return User[] Returns an array of User objects
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->orderBy('u.nameSchool', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Trouve les étudiants badgés par une école spécifique
     */
    public function findBadgedStudentsBySchool(int $schoolId): array
    {
        $connection = $this->getEntityManager()->getConnection();
        
        $sql = "
            SELECT u.id, u.email, u.first_name, u.last_name, u.badge
            FROM user u
            INNER JOIN request_badge rb ON rb.user_id = u.id
            WHERE rb.school_id = :schoolId 
            AND rb.status = 'ACCEPTED'
            AND u.badge IS NOT NULL
            ORDER BY u.first_name ASC, u.last_name ASC, u.email ASC
        ";
        
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery(['schoolId' => $schoolId]);
        
        return $result->fetchAllAssociative();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
