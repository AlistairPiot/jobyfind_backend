<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class AuthController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    // Constructeur pour injecter les services
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        // Récupération des données envoyées en JSON
        $data = json_decode($request->getContent(), true);

        // Validation des données (juste un exemple simple)
        if (empty($data['email']) || empty($data['password'])) {
            return new JsonResponse(['message' => 'Email and password are required.'], 400);
        }
        dd("coucou");

        // Création du nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        $user->setCreatedAt(new \DateTimeImmutable());  // Ajout de la date de création

        // Sauvegarde en base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], 201);
    }
}
