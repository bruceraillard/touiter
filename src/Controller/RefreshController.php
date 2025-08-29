<?php

namespace App\Controller;

use App\Entity\RefreshToken as RefreshTokenEntity;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RefreshController extends AbstractController
{
    public function __construct(
        private RefreshTokenRepository   $repo,
        private EntityManagerInterface   $em,
        private UserProviderInterface    $userProvider,
        private JWTTokenManagerInterface $jwtManager
    )
    {
    }

    #[Route('/api/token/refresh', name: 'app_token_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $rtValue = $data['refresh_token'] ?? null;
        if (!$rtValue) {
            return $this->json(['error' => 'refresh_token missing'], 400);
        }

        /** @var RefreshTokenEntity|null $rt */
        $rt = $this->repo->findOneBy(['refreshToken' => $rtValue]);
        if (!$rt) {
            return $this->json(['error' => 'invalid refresh_token'], 401);
        }
        if ($rt->getValid() < new \DateTimeImmutable()) {
            return $this->json(['error' => 'refresh_token expired'], 401);
        }

        // Charger l'utilisateur lié au refresh token
        $user = $this->userProvider->loadUserByIdentifier($rt->getUsername());

        // Générer un nouveau JWT
        $newJwt = $this->jwtManager->create($user);

        // SINGLE-USE : on invalide l'ancien RT et on émet un nouveau
        $this->em->remove($rt);

        $newRt = new RefreshTokenEntity();
        $newRt->setUsername($user->getUserIdentifier());
        $newRt->setRefreshToken(substr(bin2hex(random_bytes(64)), 0, 128));
        // TTL 14 jours
        $newRt->setValid(new \DateTimeImmutable('+14 days'));

        $this->em->persist($newRt);
        $this->em->flush();

        return $this->json([
            'token' => $newJwt,
            'refresh_token' => $newRt->getRefreshToken(),
        ], 200);
    }
}
