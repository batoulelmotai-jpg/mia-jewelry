<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $name    = $request->request->get('name');
            $email   = $request->request->get('email');
            $subject = $request->request->get('subject');
            $message = $request->request->get('message');

            $this->addFlash('success', '✦ Merci ' . $name . ' ! Votre message a bien été envoyé.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig');
    }
}