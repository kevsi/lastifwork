<?php
// src/Controller/Admin/UserController.php
namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
class UserController extends AbstractController
{
    private $security;
    private $userRepository;

    public function __construct(Security $security)
    {
        $this->security = $security;
        //$this->userRepository = $userRepository;
    }

    #[Route('/admin/users', name:"admin_users")]
    public function index(): Response
    {
        // Récupérer tous les utilisateurs
        //$users = $this->userRepository->findAll();
        
        // Récupérer les utilisateurs en ligne (via une méthode custom que vous devrez implémenter)
        //$onlineUsers = $this->userRepository->findOnlineUsers();
        
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
            'onlineUsers' => $onlineUsers,
            'currentUser' => $this->security->getUser(),
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="admin_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/users/{id}/edit", name="admin_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        // Logique d'édition à implémenter
        
        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/users/{id}/toggle-status", name="admin_user_toggle_status", methods={"POST"})
     */
    public function toggleStatus(User $user): Response
    {
        // Logique de changement de statut (actif/inactif)
        $user->setIsActive(!$user->getIsActive());
        $this->userRepository->save($user, true);
        
        return $this->redirectToRoute('admin_users');
    }
}