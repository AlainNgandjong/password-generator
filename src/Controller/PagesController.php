<?php

namespace App\Controller;

use App\Service\PasswordGenerator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
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
        return $this->render('pages/home.html.twig', [
            'password_default_length' => $request->cookies->getInt('app_length', $this->parameterBag->get('app.password_default_length')),
            'uppercase_letters' => $request->cookies->getBoolean('app_uppercase_letters'),
            'digits' => $request->cookies->getBoolean('app_digits'),
            'special_characters' => $request->cookies->getBoolean('app_special_characters'),
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


        $response =  $this->render('pages/password.html.twig',compact('password'));

        $response->headers->setCookie(
            new  Cookie('app_length', $length, new DateTimeImmutable('+5 years'))
        );

        $response->headers->setCookie(
            new  Cookie('app_uppercase_letters', $uppercaseLetters ? '1' : '0', new DateTimeImmutable('+5 years'))
        );

        $response->headers->setCookie(
            new  Cookie('app_digits', $digits ? '1' : '0', new DateTimeImmutable('+5 years'))
        );

        $response->headers->setCookie(
            new  Cookie('app_special_characters', $specialCharacters ? '1' : '0', new DateTimeImmutable('+5 years'))
        );


        return $response;
    }




}
