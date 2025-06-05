<?php

namespace App\Repository;

use App\Entity\MissionRecommendation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MissionRecommendation>
 */
class MissionRecommendationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissionRecommendation::class);
    }

    /**
     * Récupère les missions recommandées pour un étudiant spécifique
     */
    public function findRecommendationsForStudent(int $studentId): array
    {
        return $this->createQueryBuilder('mr')
            ->andWhere('mr.student = :studentId')
            ->setParameter('studentId', $studentId)
            ->orderBy('mr.recommendedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les recommandations faites par une école spécifique
     */
    public function findRecommendationsBySchool(int $schoolId): array
    {
        return $this->createQueryBuilder('mr')
            ->andWhere('mr.school = :schoolId')
            ->setParameter('schoolId', $schoolId)
            ->orderBy('mr.recommendedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si une mission est déjà recommandée à un étudiant par une école
     */
    public function isAlreadyRecommended(int $missionId, int $studentId, int $schoolId): bool
    {
        $result = $this->createQueryBuilder('mr')
            ->select('COUNT(mr.id)')
            ->andWhere('mr.mission = :missionId')
            ->andWhere('mr.student = :studentId')
            ->andWhere('mr.school = :schoolId')
            ->setParameter('missionId', $missionId)
            ->setParameter('studentId', $studentId)
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }
} 