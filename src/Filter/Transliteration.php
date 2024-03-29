<?php

namespace PlaygroundCore\Filter;

/**
 * Martin Hujer's Components
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mhujer@gmail.com so I can send you a copy immediately.
 *
 * @category    Mhujer
 * @package     Mhujer_View_Helper
 * @author      Martin Hujer mhujer@gmail.com
 */

/**
 * @category   Mhujer
 * @package    Mhujer_Filter
 * @author     Martin Hujer mhujer@gmail.com
 */
class Transliteration implements \Laminas\Filter\FilterInterface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns $value translitered to ASCII
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        //translitere specific chars
        $value = $this->transliterateCzech($value);
        $value = $this->transliterateSlovak($value);
        $value = $this->transliterateRussian($value);
        $value = $this->transliterateGerman($value);
        $value = $this->transliterateFrench($value);
        $value = $this->transliterateHungarian($value);
        $value = $this->transliteratePolish($value);
        $value = $this->transliterateDanish($value);
        $value = $this->transliterateCroatian($value);

        //split string to single characters
        $characters = mb_split("~(.)~", $value);

        $return = '';
        foreach ($characters as $character) {
            /*  maybe should contain also //IGNORE  */
            $converted = iconv("utf-8", "ASCII//TRANSLIT", $character);

            //if character was converted, strip out wrong marks
            if ($character !== $converted) {
                $return .= preg_replace('~["\'^]+~', '', $converted);
            } else {
                $return .= $converted;
            }
        }
        return $return;
    }

    /**
     * Transliterate Russian chars (Cyrillic)
     *
     * @param string $s
     * @return string
     */
    private function transliterateRussian($s)
    {
        $table = array(
            "А" => "A",
            "Б" => "B",
            "В" => "V",
            "Г" => "G",
            "Д" => "D",
            "Є" => "E",
            "Е" => "JE",
            "Ё" => "JO",
            "Ж" => "ZH",
            "З" => "Z",
            "И" => "I",
            "Й" => "J",
            "К" => "K",
            "Л" => "L",
            "М" => "M",
            "Н" => "N",
            "О" => "O",
            "П" => "P",
            "Р" => "R",
            "С" => "S",
            "Т" => "T",
            "У" => "U",
            "Ф" => "F",
            "Х" => "KH",
            "Ц" => "TS",
            "Ч" => "CH",
            "Ш" => "SH",
            "Щ" => "SHCH",
            "Ъ" => "",
            "Ы" => "Y",
            "Ь" => "",
            "Э" => "E",
            "Ю" => "JU",
            "Я" => "JA",
            "Ґ" => "G",
            "Ї" => "I",
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "є" => "e",
            "е" => "je",
            "ё" => "jo",
            "ж" => "zh",
            "з" => "z",
            "и" => "i",
            "й" => "j",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "kh",
            "ц" => "ts",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "shch",
            "ъ" => "",
            "ы" => "y",
            "ь" => "",
            "э" => "e",
            "ю" => "ju",
            "я" => "ja",
            "ґ" => "g",
            "ї" => "i"
        );
        return strtr($s, $table);
    }

    /**
     * Transliterate Czech chars
     *
     * @param string $s
     * @return string
     */
    private function transliterateCzech($s)
    {
        $table = array(
            'á' => 'a',
            'č' => 'c',
            'ď' => 'd',
            'é' => 'e',
            'ě' => 'e',
            'í' => 'i',
            'ň' => 'n',
            'ó' => 'o',
            'ř' => 'r',
            'š' => 's',
            'ť' => 't',
            'ú' => 'u',
            'ů' => 'u',
            'ý' => 'y',
            'ž' => 'z',
             'Á' => 'A',
            'Č' => 'C',
            'Ď' => 'D',
            'É' => 'E',
            'Ě' => 'E',
            'Í' => 'I',
            'Ň' => 'N',
            'Ó' => 'O',
            'Ř' => 'R',
            'Š' => 'S',
            'Ť' => 'T',
            'Ú' => 'U',
            'Ů' => 'U',
            'Ý' => 'Y',
            'Ž' => 'Z',
        );
        return strtr($s, $table);
    }

    /**
     * Transliterate German chars
     *
     * @param string $s
     * @return string
     */
    private function transliterateGerman($s)
    {
        $table = array(
            //'ä' => 'ae', //messes up with slovak -> they have ä -> a
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'oe',
            'ü' => 'ue',
            'Ä' => 'Ae',
            'Ë' => 'E',
            'Ï' => 'I',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
            'ß' => 'ss',
        );
        return strtr($s, $table);
    }

    /**
     * Transliterate French chars
     *
     * @param string $s
     * @return string
     */
    private function transliterateFrench($s)
    {
        $table = array(
            'â' => 'a',
            'ê' => 'e',
            'î' => 'i',
            'ô' => 'o',
            'û' => 'u',
            'Â' => 'A',
            'Ê' => 'E',
            'Î' => 'I',
            'Ô' => 'O',
            'Û' => 'U',
            'œ' => 'oe',
            'æ' => 'ae',
            'Ÿ' => 'Y',
            'ç' => 'c',
            'Ç' => 'C',
        );
        return strtr($s, $table);
    }

    /**
     * Transliterate Hungarian chars
     *
     * @param string $s
     * @return string
     */
    private function transliterateHungarian($s)
    {
        $table = array(
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ö' => 'o',
            'ő' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ű' => 'u',
        );
        return strtr($s, $table);
    }

    /**
     * Transliterate Polish chars
     *
     * @param string $s
     * @return string
     */
    private function transliteratePolish($s)
    {
        $table = array(
        'ą' => 'a',
        'ę' => 'e',
        'ó' => 'o',
        'ć' => 'c',
        'ł' => 'l',
        'ń' => 'n',
        'ś' => 's',
        'ż' => 'z',
        'ź' => 'z',
        'Ó' => 'O',
        'Ć' => 'C',
        'Ł' => 'L',
        'Ś' => 'S',
        'Ż' => 'Z',
        'Ź' => 'Z'
        );
        return strtr($s, $table);
    }

    /**
     * Transliterate Danish chars
     *
     * @param string $s
     * @return string
     */
    private function transliterateDanish($s)
    {
        $table = array(
        'æ' => 'ae',
        'ø' => 'oe',
        'å' => 'aa',
        'Æ' => 'Ae',
        'Ø' => 'Oe',
        'Å' => 'Aa'
        );
        return strtr($s, $table);
    }

    /**
     * Transliterate Croatian chars
     *
     * @param string $s
     * @return string
     */
    private function transliterateCroatian($s)
    {
        $table = array(
            'Č' => 'C',
            'Ć' => 'C',
            'Ž' => 'Z',
            'Š' => 'S',
            'Đ' => 'D',
            'č' => 'c',
            'ć' => 'c',
            'ž' => 'z',
            'š' => 's',
            'đ' => 'd',
        );
        return strtr($s, $table);
    }

    /**
     * Transliterate Slovak chars
     *
     * @param string $s
     * @return string
     */
    private function transliterateSlovak($s)
    {
        $table = array(
            'á' => 'a',
            'Á' => 'A',
            'ä' => 'a',
            'Ä' => 'A',
            'č' => 'c',
            'Č' => 'C',
            'ď' => 'd',
            'Ď' => 'D',
            'é' => 'e',
            'É' => 'E',
            'í' => 'i',
            'Í' => 'I',
            'ĺ' => 'l',
            'Ĺ' => 'L',
            'ľ' => 'l',
            'Ľ' => 'L',
            'ň' => 'n',
            'Ň' => 'N',
            'ó' => 'o',
            'Ó' => 'O',
            'ô' => 'o',
            'Ô' => 'O',
            'ŕ' => 'r',
            'Ŕ' => 'R',
            'š' => 's',
            'Š' => 'S',
            'ť' => 't',
            'Ť' => 'T',
            'ú' => 'u',
            'Ú' => 'U',
            'Ý' => 'Y',
            'ý' => 'y',
            'ž' => 'z',
            'Ž' => 'Z',
        );
        return strtr($s, $table);
    }
}
