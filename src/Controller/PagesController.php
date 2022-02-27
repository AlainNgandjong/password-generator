<?php

namespace App\Controller;

use App\Service\PasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{

    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag){

        $this->parameterBag = $parameterBag;
    }

    #[Route('/', name: 'app_home')]
    public function home(Request $request): Response
    {
//
        return $this->render('pages/home.html.twig', [
            'password_default_length' => $request->getSession()->get('app.length',$this->parameterBag->get('app.password_default_length')),
            'password_min_length' => $this->parameterBag->get('app.password_min_length'),
            'password_max_length' => $this->parameterBag->get('app.password_max_length'),
        ]);
    }

    #[Route('/generate-password', name: 'app_generate_password')]
    public function generatePassword(Request $request, PasswordGenerator $passwordGenerator): Response
    {


        // We make sure that the password length is always
        // at minimum {app.password_min_length}
        // and at maximum {app.password_max_length}.
        $length = max(min($request->query->getInt('length'),
            $this->parameterBag->get('app.password_max_length')),
            $this->parameterBag->get('app.password_min_length'));  // limiter la longueur length entre 8 et 60

        $uppercaseLetters = $request->query->getBoolean('uppercase_letters');
        $digits = $request->query->getBoolean('digits');
        $specialCharacters = $request->query->getBoolean('special_characters');

        $password = $passwordGenerator->generate(
            length: $length,
            wUpper: $uppercaseLetters,
            wDigit: $digits,
            wSpecChar: $specialCharacters
        );

        $session = $request->getSession();

        $session->set('app.length', $length);
        $session->set('app.uppercaseLetters', $uppercaseLetters);
        $session->set('app.digits', $digits);
        $session->set('app.specialCharacters', $specialCharacters);

        return $this->render('pages/password.html.twig',compact('password'));
    }




}
