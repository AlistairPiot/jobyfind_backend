<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Entity\MissionRecommendation;
use App\Entity\User;
use App\Repository\MissionRecommendationRepository;
use App\Repository\MissionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class MissionRecommendationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MissionRecommendationRepository $recommendationRepository,
        private MissionRepository $missionRepository,
        private UserRepository $userRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('/school/{schoolId}/students', name: 'get_school_students', methods: ['GET'])]
    public function getSchoolStudents(int $schoolId): JsonResponse
    {
        // Vérifier que l'école existe
        $school = $this->userRepository->find($schoolId);
        if (!$school || !in_array('ROLE_SCHOOL', $school->getRoles())) {
            return new JsonResponse(['error' => 'École non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer les étudiants badgés par cette école
        $students = $this->userRepository->findBadgedStudentsBySchool($schoolId);

        return new JsonResponse($students, Response::HTTP_OK);
    }

    #[Route('/missions/{missionId}/recommend', name: 'recommend_mission', methods: ['POST'])]
    public function recommendMission(int $missionId, Request $request): JsonResponse
    {  
        $data = json_decode($request->getContent(), true);

        // Valider les données requises
        if (!isset($data['students']) || !isset($data['schoolId'])) {
            return new JsonResponse(['error' => 'Étudiants et école requis'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier que la mission existe
        $mission = $this->missionRepository->find($missionId);
        if (!$mission) {
            return new JsonResponse(['error' => 'Mission non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier que l'école existe
        $school = $this->userRepository->find($data['schoolId']);
        if (!$school || !in_array('ROLE_SCHOOL', $school->getRoles())) {
            return new JsonResponse(['error' => 'École non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $recommendationsCreated = 0;
        $errors = [];

        foreach ($data['students'] as $studentId) {
            // Vérifier que l'étudiant existe
            $student = $this->userRepository->find($studentId);
            if (!$student) {
                $errors[] = "Étudiant avec l'ID $studentId non trouvé";
                continue;
            }

            // Vérifier si la recommandation existe déjà
            if ($this->recommendationRepository->isAlreadyRecommended($missionId, $studentId, $data['schoolId'])) {
                $errors[] = "Mission déjà recommandée à l'étudiant " . ($student->getFirstName() ?: $student->getEmail());
                continue;
            }

            // Créer la recommandation
            $recommendation = new MissionRecommendation();
            $recommendation->setMission($mission);
            $recommendation->setStudent($student);
            $recommendation->setSchool($school);
            $recommendation->setRecommendedAt(new \DateTimeImmutable());

            $this->entityManager->persist($recommendation);
            $recommendationsCreated++;
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => "$recommendationsCreated recommandation(s) créée(s)",
            'created' => $recommendationsCreated,
            'errors' => $errors
        ], Response::HTTP_CREATED);
    }

    #[Route('/students/{studentId}/recommended-missions', name: 'get_recommended_missions', methods: ['GET'])]
    public function getRecommendedMissions(int $studentId): JsonResponse
    {
        // Vérifier que l'étudiant existe
        $student = $this->userRepository->find($studentId);
        if (!$student) {
            return new JsonResponse(['error' => 'Étudiant non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer les recommandations pour cet étudiant
        $recommendations = $this->recommendationRepository->findRecommendationsForStudent($studentId);
        
        // Extraire les IDs des missions recommandées
        $recommendedMissionIds = array_map(function($recommendation) {
            return $recommendation->getMission()->getId();
        }, $recommendations);

        return new JsonResponse($recommendedMissionIds, Response::HTTP_OK);
    }

    #[Route('/school/{schoolId}/recommendations', name: 'get_school_recommendations', methods: ['GET'])]
    public function getSchoolRecommendations(int $schoolId): JsonResponse
    {
        // Vérifier que l'école existe
        $school = $this->userRepository->find($schoolId);
        if (!$school || !in_array('ROLE_SCHOOL', $school->getRoles())) {
            return new JsonResponse(['error' => 'École non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer les recommandations faites par cette école
        $recommendations = $this->recommendationRepository->findRecommendationsBySchool($schoolId);
        
        // Formatter les données pour le frontend
        $formattedRecommendations = [];
        foreach ($recommendations as $recommendation) {
            $mission = $recommendation->getMission();
            $student = $recommendation->getStudent();
            
            $formattedRecommendations[] = [
                'id' => $recommendation->getId(),
                'recommendedAt' => $recommendation->getRecommendedAt()->format('Y-m-d H:i:s'),
                'mission' => [
                    'id' => $mission->getId(),
                    'name' => $mission->getName(),
                    'description' => $mission->getDescription(),
                    'company' => $mission->getUser() ? $mission->getUser()->getNameCompany() : null,
                    'type' => $mission->getType() ? $mission->getType()->getName() : null
                ],
                'student' => [
                    'id' => $student->getId(),
                    'firstName' => $student->getFirstName(),
                    'lastName' => $student->getLastName(),
                    'email' => $student->getEmail()
                ]
            ];
        }

        return new JsonResponse($formattedRecommendations, Response::HTTP_OK);
    }
} 