<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class SchoolController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('/schools', name: 'get_schools', methods: ['GET'])]
    public function getSchools(): JsonResponse
    {
        // Récupérer seulement les utilisateurs avec le rôle ROLE_SCHOOL
        $schools = $this->userRepository->findByRole('ROLE_SCHOOL');

        // Sérialiser les données avec le groupe user:read
        $data = $this->serializer->serialize($schools, 'json', ['groups' => ['user:read']]);

        return new JsonResponse(json_decode($data), JsonResponse::HTTP_OK);
    }
} 