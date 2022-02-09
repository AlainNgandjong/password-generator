<?php

namespace App\Controller;

use App\Service\PasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('pages/home.html.twig');
    }

    #[Route('/generate-password', name: 'app_generate_password')]
    public function generatePassword(Request $request, PasswordGenerator $passwordGenerator): Response
    {


        $password = $passwordGenerator->generate(
            length: max(min($request->query->getInt('length'),60),8),  // limiter la longueur length entre 8 et 60
            wUpper: $request->query->getBoolean('uppercase_letters'),
            wDigit: $request->query->getBoolean('digits'),
            wSpecChar: $request->query->getBoolean('special_characters')
        );

        return $this->render('pages/password.html.twig',compact('password'));
    }




}
