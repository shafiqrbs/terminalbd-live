<?php

namespace Setting\Bundle\ToolBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

/**
 * RequestManager
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

class ToolManager

{

    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    public function  __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();
    }



    public function createDirectory($globalOption, $dir = null)
    {

        $assets_dir = $_SERVER['DOCUMENT_ROOT'].'/uploads/domain/';
        if(!file_exists($assets_dir.$globalOption)){
            if(mkdir($assets_dir.$globalOption, 0777, true)){
                //return $path;
                mkdir($assets_dir.$globalOption.'/setting', 0777, true);
                mkdir($assets_dir.$globalOption.'/setting/customize_template', 0777, true);
                mkdir($assets_dir.$globalOption.'/content', 0777, true);
                mkdir($assets_dir.$globalOption.'/inventory', 0777, true);
                mkdir($assets_dir.$globalOption.'/inventory/item/', 0777, true);
                mkdir($assets_dir.$globalOption.'/domain_user', 0777, true);
                mkdir($assets_dir.$globalOption.'/media', 0777, true);

            }else{

                return false;
            }
        }
        if(!empty($dir)){

            $path = $globalOption.'/'.$dir;
            if(!file_exists($assets_dir.$path)){
                if(mkdir($assets_dir.$path, 0777)){
                    return $path;
                }else{
                    return false;
                }
            }
            return $path = $globalOption.'/'.$dir;
        }

    }

    public function create_slide_dir($globalOption)
    {
        $assets_dir = __DIR__.'/../../../../../web/uploads/domain/';
        if(!file_exists($assets_dir.$globalOption)){
            if(mkdir($assets_dir.$globalOption, 0777)){
                mkdir($assets_dir.$globalOption.'/thumbs', 0777);
                mkdir($assets_dir.$globalOption.'/larges', 0777);
                //return $path;
            }else{
                return false;
            }
        }


    }

    public function deleteDirectory($dir)
    {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!deleteDirectory($dir . "/" . $item)) return false;
            };
        }
        return rmdir($dir);
    }

    public function delete_directory($dir)
    {
        if(is_dir($dir))
        {
            $dir = (substr($dir, -1) != "/")? $dir."/":$dir;
            $openDir = opendir($dir);
            while($file = readdir($openDir))
            {
                if(!in_array($file, array(".", "..")))
                {
                    if(!is_dir($dir.$file))
                        @unlink($dir.$file);

                }
            }
            closedir($openDir);
            @rmdir($dir);
        }
    }

    public function specialExpClean($string) {

        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^0-9,.]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '', $string); // Replaces multiple hyphens with single one.
    }

    public function intToWords($number) {

        $no = round($number);
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
            "." . $words[$point / 10] . " " .
            $words[$point = $point % 10] : '';
        if($points){
            $paisa = ' Paisa';
        }else{
            $paisa ='';
        }
        return ucwords($result) . "Taka" . ucwords($points) . $paisa;
    }



}