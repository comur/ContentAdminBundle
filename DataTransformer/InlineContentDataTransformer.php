<?php


namespace Comur\ContentAdminBundle\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class InlineContentDataTransformer implements DataTransformerInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     *
     * @param  $jsonData
     * @return string
     */
    public function transform($contentData)
    {
        if (!$contentData) {
            return "{}";
        }
//        dump($jsonData, json_decode(json_decode($jsonData, true), true));exit;
        return json_encode($contentData);
    }

    /**
     * @param  array $contentData
     * @return string json value of data
     */
    public function reverseTransform($jsonData = "{}")
    {
        return json_decode($jsonData, true);
    }
}
