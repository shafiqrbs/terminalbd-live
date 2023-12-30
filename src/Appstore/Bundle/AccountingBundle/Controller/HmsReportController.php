<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HmsReportController extends Controller
{
    public function balanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report:incomePdf.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function incomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report/Hms:income.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function pdfIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsIncome($globalOption,$data);
        $html = $this->renderView(
            'AccountingBundle:Report/Hms:incomePdf.html.twig', array(
                'globalOption' => $globalOption,
                'overview' => $overview,
                'searchForm' => $data,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="incomePdf.pdf"');
        echo $pdf;

        return new Response('');
    }

    public function printIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report/Hms:incomePdf.html.twig', array(
            'globalOption' => $globalOption,
            'overview' => $overview,
            'searchForm' => $data,
            'print' => '<script>window.print();</script>'
        ));

    }

    public function monthlyIncomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsMonthlyIncome( $this->getUser(),$data);
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
        return $this->render('AccountingBundle:Report/Hms:monthlyIncome.html.twig', array(
            'overview' => $overview,
            'dateRange' => $dateRange,
            'searchForm' => $data,
        ));
    }

    public function monthlyIncomePdfAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsMonthlyIncome( $this->getUser(),$data);
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
        $html = $this->renderView(
            'AccountingBundle:Report/Hms:monthlyIncomePdf.html.twig', array(
                'globalOption' => $globalOption,
                'dateRange' => $dateRange,
                'overview' => $overview,
                'searchForm' => $data,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="monthlyIncomePdf.pdf"');
        echo $pdf;

        return new Response('');
    }

    public function monthlyIncomePrintAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsMonthlyIncome( $this->getUser(),$data);
        exit;
        return $this->render('AccountingBundle:Report/Hms:monthlyIncomePdf.html.twig', array(
            'globalOption' => $globalOption,
            'overview' => $overview,
            'searchForm' => $data,
            'print' => '<script>window.print();</script>'
        ));

    }


}
