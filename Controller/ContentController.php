<?php

namespace Comur\ContentAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends AbstractController
{
    public function contentEditor(Request $request) {
        $content = $this->getDoctrine()->getManager()->getRepository($request->query->get('class'))->findOneBy(array(
            'template' => $request->query->get('template')
        ));

        return $this->render('@ComurContentAdmin/page_editor.html.twig', array(
            'template' => $request->query->get('template'),
            'content' => $content && $content->getContent($request->query->get('locale')) ? $content->getContent($request->query->get('locale')) : array()
        ));
    }
}
