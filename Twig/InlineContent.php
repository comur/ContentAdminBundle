<?php


namespace Comur\ContentAdminBundle\Twig;

use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use DOMWrap\Document;

class InlineContent extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('inlinecontent', [$this, 'replaceContent'], ['is_safe' => ['html']]),
        ];
    }

    public function replaceContent($html, $content = null)
    {
        if (!$content) {
            return $html;
        }

        $doc = new Document();
        $doc->html($html);
        $items = $doc->find('[data-content-id]');

        if (count($items)) {
            foreach ($items as $item) {
                if (isset($content[$item->attr('data-content-id')])) {
                    // Unfortunately we cannot use dom manipulation to replace the content as it adds fragments or escapes html
                    $default = $item->html();
                    $value = $content[$item->attr('data-content-id')];
                    $itemDefaultHtml = $item->__toString();
                    $itemHtml = str_replace($default, $value, $itemDefaultHtml);
                    $html = str_replace($itemDefaultHtml, $itemHtml, $html);
                }
            }
        }
        return $html;
    }
}
