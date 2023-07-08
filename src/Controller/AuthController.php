<?php

namespace App\Controller;

use App\Service\AccountFactory;
use App\Service\MailerService;
use App\Service\UserFactory;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends BaseController
{
    public function __construct(
        private MailerService               $mailer,
        private UserPasswordHasherInterface $passwordHasher,
        private readonly UserFactory        $userFactory,
        private readonly AccountFactory     $accountFactory,
    )
    {}
    #[Route('/api/check-email', methods: ['POST'])]
    public function checkEmail(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUserRepository()->findOneBy(['email' => $data['email']]);

            if(!$user) {
                return $this->json([
                    'error' => true,
                    'message' => 'Aucun utilisateur n\existe avec cette adresse email'
                ]);
            }

            $token = bin2hex(random_bytes(50));
            $user->setResetToken($token);

            $this->getUserRepository()->save($user, true);

            $urlLink = 'http://localhost:8000/reset-password?token='.$user->getResetToken().'&email='.$user->getEmail();

            $this->mailer->sendResetPasswordEmail($user, $urlLink);

            return $this->json([
                'message' => 'Un email est parti sur cette adresse : '. $user->getEmail()
            ]);
        } catch (\Error $e) {
            return $this->json(['message' => $e]);
        }
    }

    #[Route('/api/verify-user', name: 'api_verify_user', methods: ['GET'])]
    public function verifyTokenAndEmail(Request $request): Response
    {
        try {
            $token = $request->get('token');

            $user = $this->getUserRepository()->findOneBy([
                'email' => $request->get('email'),
                'resetToken' => $request->get('token')
            ]);

            if(!$user) {
                throw new AccessDeniedException('Le token n\'est pas bon ou personne n\'existe avec cette adresse email.');
            }

            return $this->json($token);
        } catch (\Error $e) {
            return $this->json(['message' => $e]);
        }
    }

    #[Route('/api/reset-password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUserRepository()->findOneBy([
                'email' => $request->get('email'),
                'resetToken' => $request->get('token')
            ]);

            if($data['confirmPassword']) {
                if($data['confirmPassword'] !== $data['password']) {
                    return $this->json([
                        'error' => true,
                        'message' => 'Les deux mots de passe doivent correspondre'
                    ]);
                } else {
                    $user->setPassword($this->passwordHasher->hashPassword(
                        $user,
                        $data['password']
                    ));
                    $user->setResetToken(null);
                    $this->getUserRepository()->save($user, true);
                }
            } else {
                return $this->json([
                    'error' => true,
                    'message' => 'Tu dois confirmer ton mot de passe, wesh'
                ]);
            }

            return $this->json([
                'message' => 'Le mot de passe a été modifié, tu peux te reconnecter ;)'
            ]);
        } catch (\Error $e) {
            return $this->json(['message' => $e]);
        }
    }

    #[Route('/api/profile/update', name: 'api_profile_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->getUser();

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $this->userFactory->update($user, $data);

            $account = $user->getAccount();

            if (!$account) {
                throw $this->createNotFoundException();
            }

            $this->accountFactory->update($account, $data);

            $response = $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($user));

//            $token = $this->getJWTTokenManagerInterface()->create($user);
//            $response->setContent(json_encode(['token' => $token]));

            return $response;
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }

    #[Route('/api/user', name: 'app_get_user', methods: ['GET'])]
    public function getAuthenticatedUser(): Response
    {
        try {
            $user = $this->getUser();

            if (!$user) {
                throw $this->createNotFoundException();
            }

            return $this->getApiService()->setResponse($this->getApiService()->handleCircularReference($user));
        } catch (\throwable $e) {
            return $this->getApiService()->setResponse($e->getMessage(), $e);
        }
    }
}