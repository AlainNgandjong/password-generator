<?php

namespace App\Controller;

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
    public function generatePassword(Request $request): Response
    {
        $length = $request->query->getInt('length');
        $uppercaseLetters = $request->query->getBoolean('uppercase_letters');
        $digits = $request->query->getBoolean('digits');
        $specialCharacters = $request->query->getBoolean('special_characters');

        $characters = range('a', 'z');

        if($uppercaseLetters){
            $characters = array_merge($characters , range('A', 'Z'));
        }
        if($digits){
            $characters = array_merge($characters , range(0, 9));
        }
        if($specialCharacters){
            $characters = array_merge(
                $characters ,
                ['!', '#', '$', '%', '&', '(', ')', '*', '+', ',', '-', '.', '/',
                     '=', '?', '@', '[', '\\', ']', '^', '_', '{', '|', '}', '~']
            );
        }

//        shuffle($characters); // mix the array list if needed

        $password = '';

        for ($i=0; $i < $length; $i++) {
            $password .= $characters[random_int(0, count($characters) - 1)];
//            $password = $password.$characters[mt_rand(0, count($characters) - 1)];
//            $password .= $characters[mt_rand(0, count($characters) - 1)];

        }

        return $this->render('pages/password.html.twig',compact('password'));
    }
}
