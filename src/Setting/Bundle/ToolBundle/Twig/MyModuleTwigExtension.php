<?php

namespace Setting\Bundle\ToolBundle\Twig;


/**
 * Class MyModuleTwigExtension
 */
class MyModuleTwigExtension extends \Twig_Extension {

    //  A list with all the defined functions. The first parameter is the name by which we will call the function. The second parameter is the name of the function that will implement the function.
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('color_text', [$this, 'colorText']),
        ];
    }

    //  A list with all the defined filters. The first parameter is the name by which we will call the filter. The second parameter is the name of the function that will implement the filter.
    public function getFilters() {
        return [
            new \Twig_SimpleFilter('in_words', [$this, 'intToWords']),
            new \Twig_SimpleFilter('money_format', [$this, 'moneyFormat']),
            new \Twig_SimpleFilter('cut_text', [$this, 'cutText']),
        ];
    }



    // Returns the extension name
    public function getName() {
        return 'MyModule.MyModuleTwigExtension';
    }


    // Function that returns an example text with a color passed as a parameter.
    public function colorText($color = "black", $text = 'Lorem ipsum dolor sit amet,
 consectetur adipiscing elit.  Quos quidem tibi studiose et diligenter
 tractandos magnopere censeo.') {
        return '<span style="color: ' . $color . '"> ' . $text . ' </span>';
    }

    // Filter to convert a number into €
    public function moneyFormat($string) {
        setlocale(LC_MONETARY, 'es_ES.UTF-8');
        return money_format('%i', $string);
    }


    // Filter that cuts a text according to the maximum number of characters. You can also choose which characters will be put at the end of the text. By default is ‘...’
    public function cutText($text, $maxchar, $end = '...') {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);
            $output = '';
            $i = 0;
            while (1) {
                $length = strlen($output)+strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                }
                else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        }
        else {
            $output = $text;
        }
        return $output;
    }


    public function intToWords($number) {

        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred
                    :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $points = ($point) ?
            $words[$point / 10] . " " .
            ($words[$point = $point % 10] ?: 'zero') : '';
        if($points){
            $paisa = ' Paisa';
        }else{
            $paisa ='';
        }
        return ucwords($result) . "Taka " . ucwords($points) . $paisa ." Only";
    }

}