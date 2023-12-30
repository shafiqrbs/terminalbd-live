<?php
/**
 * Created by PhpStorm.
 * User: rbs
 * Date: 12/24/15
 * Time: 11:29 AM
 */

namespace Setting\Bundle\ToolBundle\Controller;

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;



/**
 * BarCode controller.
 *
 */
class BarCodeController extends Controller
{
    /**
     * Display code as a png image
     */
    public function barcodeAction()
    {

        $barcode = new BarcodeGenerator();
        $barcode->setText('12345678');
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(20);
        $barcode->setFontSize(10);
        $code = $barcode->generate();
        echo '<img src="data:image/png;base64,'.$code.'" />';

        exit;

    }


}