<?php

namespace App\Lib;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Static class to translate code
 */
class Trans
{
    private static ?TranslatorInterface $translator = null;

    /**
     * Set Symfony translator
     * @param TranslatorInterface $translator Symfony trnslator
     */
    public static function setup(?TranslatorInterface $translator): void
    {
        self::$translator=$translator;
    }

    /**
     * Translate using the message domain
     * 
     * @param string $text Translation code
     * @param array $params Translation parameters
     * 
     * @return string Translated code
     */
    public static function tr(string $text, array $params=[]): string
    {
        if(self::$translator){
            $star='';
            if(substr($text, -1) == '*'){
                $star='*';
                $text=rtrim($text, '*');
            }
            return self::$translator->trans($text,$params).$star;
        }
        return $text;
    }


}