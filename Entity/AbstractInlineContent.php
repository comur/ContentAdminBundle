<?php


namespace Comur\ContentAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class AbstractInlineContent
{

    /**
     * @ORM\Column(type="array")
     * @var array
     */
    protected $content = array();

    /**
     * @return array
     */
    public function getContent($locale = null)
    {
        return $locale ? isset($this->content[$locale]) ? $this->content[$locale] : array() : $this->content;
    }

    /**
     * @param array $content
     */
    public function setContent($content, $locale = null)
    {
        if ($locale) {
            $this->content[$locale] = $content;
        } else {
            $this->content = $content;
        }
    }

}
