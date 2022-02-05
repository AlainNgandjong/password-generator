<?php


namespace App\Service;


class PasswordGenerator
{
    public function generate(int $length, bool $wUpper = false, bool $wDigit = false, bool $wSpecChar = false): string
    {
        $lowercaseLettersAlphabet = range('a', 'z');
        $uppercaseLettersAlphabet = range('A', 'Z');
        $digitsAlphabet = range('0', '9');
        $specialCharactersAlphabet = ['!', '#', '$', '%', '&', '(', ')',
            '*', '+', ',', '-', '.', '/', '=',
            '?', '@', '[', '\\', ']', '^', '_',
            '{', '|', '}', '~'];

        $characters = $lowercaseLettersAlphabet;

        // Add random lowercase letter
        $password = [$this->pickRandomItemFromAlphabet($lowercaseLettersAlphabet)];

        $mapping = [
            [$wUpper, $uppercaseLettersAlphabet],
            [$wDigit, $digitsAlphabet],
            [$wSpecChar, $specialCharactersAlphabet]
        ];
        foreach ($mapping as [$constraintEnabled , $constraintAlphabet]){
            if($constraintEnabled){
                $characters = [...$characters , ...$constraintAlphabet]; // or array_merge($characters , $constraintAlphabet)
                $password[] = $this->pickRandomItemFromAlphabet($constraintAlphabet);
            }
        }

        $numberOfCharacterRemaining = $length - count($password);

        for ($i = 0; $i < $numberOfCharacterRemaining; $i++) {
            $password[] = $this->pickRandomItemFromAlphabet($characters);
        }

        $password = $this->secureShuffle($password); // mix the array list if needed

        return implode('',$password); // convert array list to string

    }

    private function secureShuffle(array $arr): array
    {
        // Source : https://github.com/lamansky/secure-shuffle/blob/master/src/functions.php

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