<?php


namespace Comur\ContentAdminBundle\Form;

use Comur\ContentAdminBundle\DataTransformer\InlineContentDataTransformer;
//use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;

class InlineContentType extends AbstractType
{
    private $transformer;

    private $locales;

    private $comurImageEnabled;

    private $optionsResolver;

    public function __construct(InlineContentDataTransformer $transformer, $locales, $comurImageEnabled)
    {
        $this->transformer = $transformer;
        $this->locales = $locales;
        $this->comurImageEnabled = $comurImageEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['class']) {
            throw new \Exception('The required option "class" is missing when using ComurContentAdminBundle InlineContentType');
        }
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->optionsResolver = $resolver;
        $resolver->setDefaults([
            'template_field_name' => 'template',
            'template' => null,
            'iframe_height' => 500,
            'class' => null,
            'comur_image_params' => array(),
            'locales' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Mimic ComurImageBundle for this as we don't really use CroppableImageType
        $comurImageBundleConfig = [];
        if ($this->comurImageEnabled) {
            $comurImageBundleConfig['comur_image_params'] = [
                'cropConfig' => \Comur\ImageBundle\Form\Type\CroppableImageType::getCropConfigNormalizer(\Comur\ImageBundle\Form\Type\CroppableImageType::$cropConfig)(
                    $this->optionsResolver,
                    isset($options['comur_image_params']['cropConfig']) ? $options['comur_image_params']['cropConfig'] : array()
                ),
                'uploadConfig' => \Comur\ImageBundle\Form\Type\CroppableImageType::getUploadConfigNormalizer(\Comur\ImageBundle\Form\Type\CroppableImageType::$uploadConfig)(
                    $this->optionsResolver,
                    isset($options['comur_image_params']['uploadConfig']) ? $options['comur_image_params']['uploadConfig'] : array()
                )
            ];
        }


        $view->vars = $view->vars + array(
                'template_field_name' => $options['template_field_name'],
                'iframe_height' => $options['iframe_height'],
                'class' => $options['class'],
                'locales' => $options['locales'] ?: $this->locales,
                'comur_image_enabled' => $this->comurImageEnabled,
                'object' => $form->getParent()->getData()
            ) + $comurImageBundleConfig;
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
