<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MedicineReportController extends Controller
{
    public function balanceAction()
    {

    }

    public function incomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineIncome($user,$data);
        if(empty($data['pdf'])){
            return $this->render('AccountingBundle:Report/Medicine:income.html.twig', array(
                'overview' => $overview,
                'searchForm' => $data,
            ));
        }else{
            $html = $this->renderView(
                'AccountingBundle:Report/Medicine:incomePdf.html.twig', array(
                    'overview' => $overview,
                    'globalOption' => $this->getUser()->getGlobalOption(),
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'income.pdf');
        }
    }

    public function downloadPdf($html,$fileName = '')
    {
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename='{$fileName}'");
        echo $pdf;
        return new Response('');
    }

    public function monthlyIncomeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        if(!empty($data['startMonth']) and !empty($data['endMonth'])){
            $sm = "01-{$data['startMonth']}-{$data['year']}";
            $compareTo = new \DateTime($sm);
            $startMonth =  $compareTo->format('F');
            $endm = "01-{$data['endMonth']}-{$data['year']}";
            $compareTo = new \DateTime($endm);
            $endMonth =  $compareTo->format('F,Y');
            $dateRange = $startMonth .' To '.$endMonth;
        }else{
            $compareTo = new \DateTime("now");
            $endMonth =  $compareTo->format('F,Y');
            $dateRange =  $compareTo->format('F,Y');
        }

        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineMonthlyIncome($this->getUser(),$data);

        if(!empty($data['startMonth']) and !empty($data['endMonth'])){
            $sm = "01-{$data['startMonth']}-{$data['year']}";
            $compareTo = new \DateTime($sm);
            $startMonth =  $compareTo->format('F');
            $endm = "01-{$data['endMonth']}-{$data['year']}";
            $compareTo = new \DateTime($endm);
            $endMonth =  $compareTo->format('F,Y');
            $dateRange = $startMonth .' To '.$endMonth;
        }else{
            $compareTo = new \DateTime("now");
            $dateRange =  $compareTo->format('F,Y');
        }
        if(empty($data['pdf'])){
            return $this->render('AccountingBundle:Report/Medicine:monthlyIncome.html.twig', array(
                'overview' => $overview,
                'searchForm' => $data,
            ));
        }else{
            $html = $this->renderView(
                'AccountingBundle:Report/Medicine:monthlyIncomePdf.html.twig', array(
                    'overview' => $overview,
                    'globalOption' => $this->getUser()->getGlobalOption(),
                    'dateRange' => $dateRange,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'monthlyIncomePdf.pdf');
        }
    }


}
