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
                $contentId = $item->attr('data-content-id');
                if (isset($content[$contentId])) {
                    // Unfortunately we cannot use dom manipulation to replace the content as it adds fragments or escapes html
                    // Other problem, this fixes HTML issues so replace does not work if (for eg) there are more than 1 space between 2 attributes...
                    if (!$item->is('img')) {
                        $default = $item->html();
                        $value = $content[$contentId];
                        $itemDefaultHtml = $doc->saveXml($item);
                    } else {
                        $default = $item->attr('src');
                        $value = $content[$contentId];
                        $itemDefaultHtml = $doc->saveXml($item);
                    }
                    $itemHtml = str_replace($default, $value, $itemDefaultHtml);
                    $html = str_replace($itemDefaultHtml, $itemHtml, $html);
                }
            }
        }
        return $html;
    }
}
