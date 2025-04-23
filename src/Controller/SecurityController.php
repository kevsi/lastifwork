<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route("/login", name: "app_login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, redirigez-le
        if ($this->getUser()) {
            return $this->redirectToRoute("app_dashboard"); // Changez pour votre route d'accueil
        }

        // Récupérez l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render("security/login.html.twig", [
            "last_username" => $lastUsername,
            "error" => $error,
        ]);
    }

    #[Route("/register", name: "app_register")]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod("POST")) {
            // Récupération des données du formulaire
            $email = $request->request->get("email");
            $firstName = $request->request->get("first_name"); // Assurez-vous que le nom du champ correspond
            $lastName = $request->request->get("last_name"); // Assurez-vous que le nom du champ correspond
            $password = $request->request->get("password");
            $terms = $request->request->get("terms");

            // Vérification que les CGU sont acceptées
            if (!$terms) {
                $this->addFlash(
                    "error",
                    'Vous devez accepter les conditions générales d\'utilisation.'
                );
                return $this->redirectToRoute("app_register");
            }

            // Vérifier si l'email existe déjà
            $existingUser = $entityManager
                ->getRepository(User::class)
                ->findOneBy(["email" => $email]);
            if ($existingUser) {
                $this->addFlash(
                    "register_error",
                    "Cet email est déjà utilisé."
                );
                return $this->redirectToRoute("app_register");
            }

            // Création du nouvel utilisateur
            $user = new User();
            $user->setEmail($email);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setRoles(["ROLE_USER"]);
            $user->setIsVerified(false);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());

            // Hashage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            // Enregistrement en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Message de succès
            $this->addFlash(
                "success",
                "Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter."
            );

            return $this->redirectToRoute("app_login");
        }

        return $this->render("security/register.html.twig");
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        // Cette méthode ne sera jamais exécutée,
        // car la déconnexion est gérée par le firewall de Symfony
    }
}
