<?php
// src/Controller/Admin/AdminuserController.php
namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class AdminuserController extends AbstractController
{
    

    #[Route('/adminuser', name:"user_admin")]
    public function index(): Response
    {
        
        
        return $this->render('adminuser/index.html.twig', [
            'controller_name' => 'AdminuserController',
        ]);
    }

    


   

}