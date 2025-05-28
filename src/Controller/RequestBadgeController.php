<?php

namespace App\Controller;

use App\Entity\RequestBadge;
use App\Entity\User;
use App\Repository\RequestBadgeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class RequestBadgeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestBadgeRepository $requestBadgeRepository,
        private UserRepository $userRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('/request_badges/school/{schoolId}', name: 'get_school_badge_requests', methods: ['GET'])]
    public function getSchoolBadgeRequests(int $schoolId): JsonResponse
    {
        // Vérifier que l'école existe
        $school = $this->userRepository->find($schoolId);
        if (!$school || !in_array('ROLE_SCHOOL', $school->getRoles())) {
            return new JsonResponse(['error' => 'École non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer les demandes de badge en attente pour cette école spécifique
        $requests = $this->requestBadgeRepository->findBy([
            'school' => $school,
            'responseDate' => null
        ]);

        // Sérialiser les données
        $data = $this->serializer->serialize($requests, 'json', ['groups' => ['request_badge:read']]);

        return new JsonResponse(json_decode($data), Response::HTTP_OK);
    }

    #[Route('/request_badges/user/{userId}', name: 'get_user_badge_requests', methods: ['GET'])]
    public function getUserBadgeRequests(int $userId): JsonResponse
    {
        // Vérifier que l'utilisateur existe
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer les demandes de badge pour cet utilisateur spécifique
        $requests = $this->requestBadgeRepository->findBy(['user' => $user]);

        // Sérialiser les données
        $data = $this->serializer->serialize($requests, 'json', ['groups' => ['request_badge:read']]);

        return new JsonResponse(json_decode($data), Response::HTTP_OK);
    }

    #[Route('/request_badges', name: 'create_badge_request', methods: ['POST'])]
    public function createBadgeRequest(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Valider les données requises
        if (!isset($data['user']) || !isset($data['school'])) {
            return new JsonResponse(['error' => 'Utilisateur et école requis'], Response::HTTP_BAD_REQUEST);
        }

        // Extraire les IDs des URLs
        $userId = (int) basename($data['user']);
        $schoolId = (int) basename($data['school']);

        // Vérifier que l'utilisateur et l'école existent
        $user = $this->userRepository->find($userId);
        $school = $this->userRepository->find($schoolId);

        if (!$user || !$school) {
            return new JsonResponse(['error' => 'Utilisateur ou école non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if (!in_array('ROLE_SCHOOL', $school->getRoles())) {
            return new JsonResponse(['error' => 'L\'entité spécifiée n\'est pas une école'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier qu'il n'y a pas déjà une demande en cours
        $existingRequest = $this->requestBadgeRepository->findOneBy([
            'user' => $user,
            'school' => $school,
            'responseDate' => null
        ]);

        if ($existingRequest) {
            return new JsonResponse(['error' => 'Une demande est déjà en cours pour cette école'], Response::HTTP_CONFLICT);
        }

        // Créer la nouvelle demande
        $badgeRequest = new RequestBadge();
        $badgeRequest->setUser($user);
        $badgeRequest->setSchool($school);
        $badgeRequest->setRequestDate(new \DateTimeImmutable($data['requestDate'] ?? 'now'));
        $badgeRequest->setStatus('PENDING');

        $this->entityManager->persist($badgeRequest);
        $this->entityManager->flush();

        // Sérialiser la réponse
        $responseData = $this->serializer->serialize($badgeRequest, 'json', ['groups' => ['request_badge:read']]);

        return new JsonResponse(json_decode($responseData), Response::HTTP_CREATED);
    }

    #[Route('/request_badges/{id}/accept', name: 'accept_badge_request', methods: ['PATCH'])]
    public function acceptBadgeRequest(int $id): JsonResponse
    {
        $badgeRequest = $this->requestBadgeRepository->find($id);
        
        if (!$badgeRequest) {
            return new JsonResponse(['error' => 'Demande non trouvée'], Response::HTTP_NOT_FOUND);
        }

        if ($badgeRequest->getResponseDate()) {
            return new JsonResponse(['error' => 'Cette demande a déjà été traitée'], Response::HTTP_BAD_REQUEST);
        }

        // Accepter la demande
        $badgeRequest->setResponseDate(new \DateTimeImmutable());
        $badgeRequest->setStatus('ACCEPTED');

        // Mettre à jour l'utilisateur avec le badge
        $user = $badgeRequest->getUser();
        $user->setBadge(new \DateTimeImmutable());

        $this->entityManager->flush();

        // Sérialiser la réponse
        $responseData = $this->serializer->serialize($badgeRequest, 'json', ['groups' => ['request_badge:read']]);

        return new JsonResponse(json_decode($responseData), Response::HTTP_OK);
    }

    #[Route('/request_badges/{id}/reject', name: 'reject_badge_request', methods: ['PATCH'])]
    public function rejectBadgeRequest(int $id): JsonResponse
    {
        $badgeRequest = $this->requestBadgeRepository->find($id);
        
        if (!$badgeRequest) {
            return new JsonResponse(['error' => 'Demande non trouvée'], Response::HTTP_NOT_FOUND);
        }

        if ($badgeRequest->getResponseDate()) {
            return new JsonResponse(['error' => 'Cette demande a déjà été traitée'], Response::HTTP_BAD_REQUEST);
        }

        // Refuser la demande
        $badgeRequest->setResponseDate(new \DateTimeImmutable());
        $badgeRequest->setStatus('REJECTED');

        $this->entityManager->flush();

        // Sérialiser la réponse
        $responseData = $this->serializer->serialize($badgeRequest, 'json', ['groups' => ['request_badge:read']]);

        return new JsonResponse(json_decode($responseData), Response::HTTP_OK);
    }

    #[Route('/badged_students/school/{schoolId}', name: 'get_school_badged_students', methods: ['GET'])]
    public function getSchoolBadgedStudents(int $schoolId): JsonResponse
    {
        // Vérifier que l'école existe
        $school = $this->userRepository->find($schoolId);
        if (!$school || !in_array('ROLE_SCHOOL', $school->getRoles())) {
            return new JsonResponse(['error' => 'École non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer les demandes de badge acceptées pour cette école spécifique
        $acceptedRequests = $this->requestBadgeRepository->findBy([
            'school' => $school,
            'status' => 'ACCEPTED'
        ]);

        // Extraire les utilisateurs badgés
        $badgedStudents = [];
        foreach ($acceptedRequests as $request) {
            $user = $request->getUser();
            if ($user && $user->getBadge()) {
                $badgedStudents[] = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'badge' => $user->getBadge()->format('Y-m-d H:i:s'),
                    'requestId' => $request->getId()
                ];
            }
        }

        return new JsonResponse($badgedStudents, Response::HTTP_OK);
    }

    #[Route('/remove_badge/{userId}', name: 'remove_badge', methods: ['DELETE'])]
    public function removeBadge(int $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if (!$user->getBadge()) {
            return new JsonResponse(['error' => 'Cet utilisateur n\'a pas de badge'], Response::HTTP_BAD_REQUEST);
        }

        // Retirer le badge
        $user->setBadge(null);

        // Optionnel : marquer les demandes de badge comme annulées
        $badgeRequests = $this->requestBadgeRepository->findBy([
            'user' => $user,
            'status' => 'ACCEPTED'
        ]);

        foreach ($badgeRequests as $request) {
            $request->setStatus('REVOKED');
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Badge retiré avec succès'], Response::HTTP_OK);
    }
} 