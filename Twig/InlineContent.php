<?php


namespace Comur\ContentAdminBundle\Twig;

use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
//use DOMWrap\Document;

class InlineContent extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('inlinecontent', [$this, 'replaceContent'], ['is_safe' => ['html']]),
        ];
    }

    /* NOT USED ANYMORE AS THERE IS A BUG IN THIS PACKAGE
    public function replaceContentOld($html, $content = null)
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
                        if ($item->is('span') || $item->is('p') || $item->is('h')) {
                            $item->html($content[$contentId]);
                        } else {
                            $item->html($content[$contentId]);
                        }
                    } else {
                        $default = $item->attr('src');
                        $value = $content[$contentId];
                        $itemDefaultHtml = $doc->saveXml($item);
                        $item->attr('src', $content[$contentId]);
                    }
//                    $itemHtml = str_replace($default, $value, $itemDefaultHtml);
//                    $html = str_replace($itemDefaultHtml, $itemHtml, $html);
                }
            }
        }
        return implode("\n", array_map(function($item) use ($doc) { return $doc->saveXML($item); }, $doc->find('body')->children()->toArray()));
    }
    */

    public function replaceContent($html, $content = null)
    {
        if (!$content) return $html;

        $doc = str_get_html($html);
        $items = $doc->find('[data-content-id]');

        if (count($items)) {
            foreach ($items as $item) {
                $contentId = $item->getAttribute('data-content-id');
                if (isset($content[$contentId])) {
                    if ($item->tag !== 'img') {
                        $item->innertext = $content[$contentId];
                    } else {
                        $item->src = $content[$contentId];
                    }
                }
                if ($item->tag === 'img' && isset($content[$contentId.'Alt'])) {
                    $item->alt = $content[$contentId.'Alt'];
                }
            }
        }
        return $doc->save();
    }
}
