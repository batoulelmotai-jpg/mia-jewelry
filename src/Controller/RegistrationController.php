<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');
            $password = $request->request->get('password');

            if ($email && $password) {

                $user = new User();
                $user->setEmail($email);

                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Compte créé avec succès.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig');
    }
}