<?php

namespace Comur\ContentAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends AbstractController
{
    public function contentEditor(Request $request) {
        $templatesParam = $this->container->getParameter('comur_content_admin.templates_parameter');
        $templates = $this->container->getParameter($templatesParam);

        $request->setLocale($request->query->get('locale'));

        if ($request->query->has('entityid') && $request->query->get('entityid')) {
            $content = $this->getDoctrine()->getManager()->getRepository($request->query->get('class'))->findOneBy(array(
                'id' => $request->query->get('entityid')
            ));
        } else {
            $content = $this->getDoctrine()->getManager()->getRepository($request->query->get('class'))->findOneBy(array(
                'template' => $request->query->get('template')
            ));
        }

        $templateConfig = null;

        if ($templates && count($templates)) {
            foreach ($templates as $template) {
                if ($template['template'] === $request->query->get('template')) {
                    $templateConfig = $template;
                    break;
                }
            }
        }

        return $this->render('@ComurContentAdmin/page_editor.html.twig', array(
            'template' => $request->query->get('template'),
            'templateConfig' => $templateConfig,
            'content' => $content && $content->getContent($request->query->get('locale')) ? $content->getContent($request->query->get('locale')) : array(),
            $this->container->getParameter('comur_content_admin.entity_name') => $content,
            'showImageSize' => $this->container->getParameter('comur_content_admin.show_image_size')
        ));
    }
}
