<?php

namespace Frontend\FrontentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebServicePageController extends Controller
{

    public function emailSending(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:ContactMessage')->insertMessage($data);
        return new Response('success');
        //$referer = $request->headers->get('referer');
        //return new RedirectResponse($referer);

    }

    public function smsSending(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:ContactMessage')->insertMessage($data);
        return new Response('success');
        //$referer = $request->headers->get('referer');
        //return new RedirectResponse($referer);

    }

    public function blogSubmitAction(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:BlogComment')->insertMessage($data);
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

    }

    public function admissionSubmitAction(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:AdmissionComment')->insertMessage($data);
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

    }


}
