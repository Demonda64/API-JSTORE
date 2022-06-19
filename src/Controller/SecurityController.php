<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class SecurityController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
{
    $this->manager = $manager;
}

    /**
     * @Route("/register", name="app_security_register")
     */
    public function register(Request $request,UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        //analyse de la requete par le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //Traitement des données reçues du formulaire
            $password_hash = $hasher-> hashPassword($user,$user->getPassword());

            $user->setPassword($password_hash);

            $this->manager->persist($user);
            $this->manager->flush();
            return $this->redirectToRoute("security_login");
            //dd($user);
        }

        return $this->render('security/index.html.twig', [
            'controller_name' => 'Inscription',
            'form' => $form->createView(),
        ]);
    }
        /**
         * @Route("/login", name="security_login")
         */
        public function login(): Response{

            return $this->render('security/login.html.twig');
    }

}
