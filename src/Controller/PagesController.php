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


        $lowercaseLettersAlphabet = range('a', 'z');
        $uppercaseLettersAlphabet = range('A', 'Z');
        $digitsAlphabet = range('0', '9');
        $specialCharactersAlphabet = ['!', '#', '$', '%', '&', '(', ')', '*', '+', ',', '-', '.', '/',
            '=', '?', '@', '[', '\\', ']', '^', '_', '{', '|', '}', '~'];

        $characters = $lowercaseLettersAlphabet;
        $password = '';

        // Add random lowercase letter
        $password .= $this->pickRandomItemFromAlphabet($lowercaseLettersAlphabet);

        if($uppercaseLetters){
            $characters = array_merge($characters ,$uppercaseLettersAlphabet);

            // Add random upercase letter
            $password .= $this->pickRandomItemFromAlphabet($uppercaseLettersAlphabet);
        }
        if($digits){
            $characters = array_merge($characters , $digitsAlphabet);

            // Add random digit
            $password .= $this->pickRandomItemFromAlphabet($digitsAlphabet);
        }
        if($specialCharacters){
            $characters = array_merge($characters , $specialCharactersAlphabet);

            // Add special character
            $password .= $this->pickRandomItemFromAlphabet($specialCharactersAlphabet);
        }


        $numberOfCharacterRemaining = $length - mb_strlen($password);


        for ($i=0; $i < $numberOfCharacterRemaining; $i++) {
            $password .= $this->pickRandomItemFromAlphabet($characters);
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

    private function pickRandomItemFromAlphabet(array $alphabet): string
    {
        return $alphabet[random_int(0, count($alphabet) -1)];
    }
}
