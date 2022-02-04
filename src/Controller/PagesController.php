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


        $lowercaseLettersSet = range('a', 'z');
        $uppercaseLettersSet = range('A', 'Z');
        $digitsSet = range('0', '9');
        $specialCharactersSet = ['!', '#', '$', '%', '&', '(', ')', '*', '+', ',', '-', '.', '/',
            '=', '?', '@', '[', '\\', ']', '^', '_', '{', '|', '}', '~'];

        $characters = $lowercaseLettersSet;
        $password = '';

        // Add random lowercase letter
        $password .= $lowercaseLettersSet[random_int(0, count($lowercaseLettersSet) -1)];

        if($uppercaseLetters){
            $characters = array_merge($characters ,$uppercaseLettersSet);

            // Add random upercase letter
            $password .= $uppercaseLettersSet[random_int(0, count($uppercaseLettersSet) -1)];
        }
        if($digits){
            $characters = array_merge($characters , $digitsSet);

            // Add random digit
            $password .= $digitsSet[random_int(0, count($digitsSet) -1)];
        }
        if($specialCharacters){
            $characters = array_merge($characters , $specialCharactersSet);

            // Add special character
            $password .= $specialCharactersSet[random_int(0, count($specialCharactersSet) -1)];
        }


        $numberOfCharacterRemaining = $length - mb_strlen($password);


        for ($i=0; $i < $numberOfCharacterRemaining; $i++) {
            $password .= $characters[random_int(0, count($characters) - 1)];
//            $password = $password.$characters[mt_rand(0, count($characters) - 1)];
//            $password .= $characters[mt_rand(0, count($characters) - 1)];

        }
        $password = str_split($password);
        $password = $this->secureShuffle($password); // mix the array list if needed

        $password = implode('',$password); // convert array list to string

        return $this->render('pages/password.html.twig',compact('password'));
    }

    private function secureShuffle(array $arr): array
    {
//        Source : https://github.com/lamansky/secure-shuffle/blob/master/src/functions.php

        $length = count($arr);
        for ($i = $length - 1; $i > 0; $i --){
            $j = random_int(0, $i);
            $temp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $temp;
        }
        return $arr;
    }
}
