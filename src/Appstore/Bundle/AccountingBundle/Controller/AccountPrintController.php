<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;



/**
 * AccountSales controller.
 *
 */
class AccountPrintController extends Controller
{


	public function getBarcode($value)
	{
		$barcode = new BarcodeGenerator();
		$barcode->setText($value);
		$barcode->setType(BarcodeGenerator::Code39Extended);
		$barcode->setScale(1);
		$barcode->setThickness(25);
		$barcode->setFontSize(8);
		$code = $barcode->generate();
		$data = '';
		$data .= '<img src="data:image/png;base64,'.$code .'" />';
		return $data;
	}

	public function salesPrintAction(AccountSales $sales)
    {
    	$em = $this->getDoctrine()->getManager();
	    $template = $sales->getGlobalOption()->getSlug();
	    $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($sales->getGlobalOption(), $data = array('mobile'=> $sales->getCustomer()->getMobile()));
	    $balance = empty($result) ? 0 :$result[0]['customerBalance'];
	    $amountInWords = $this->get('settong.toolManageRepo')->intToWords($sales->getAmount());
	    $barcode = $this->getBarcode($sales->getAccountRefNo());
	    return  $this->render("AccountingBundle:Print/sales:print.html.twig",
		    array(
			    'amountInWords' => $amountInWords,
			    'barcode' => $barcode,
			    'entity' => $sales,
			    'balance' => $balance,
			    'print' => 'print',
		    )
	    );
    }

	public function purchasePrintAction(AccountPurchase $sales)
	{
		$em = $this->getDoctrine()->getManager();
		$template = $sales->getGlobalOption()->getSlug();
		$result = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorBusinessOutstanding($sales->getGlobalOption(), $data = array('mobile'=> $sales->getCustomer()->getMobile()));
		$balance = empty($result) ? 0 :$result[0]['customerBalance'];
		$amountInWords = $this->get('settong.toolManageRepo')->intToWords($sales->getPayment());
		$barcode = $this->getBarcode($sales->getAccountRefNo());
		return  $this->render("AccountingBundle:Print/purchase:print.html.twig",
			array(
				'amountInWords' => $amountInWords,
				'barcode' => $barcode,
				'entity' => $sales,
				'balance' => $balance,
				'print' => 'print',
			)
		);
	}


}
