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

class InlineContentType extends AbstractType
{
    private $transformer;

    private $locales;

    public function __construct(InlineContentDataTransformer $transformer, $locales)
    {
        $this->transformer = $transformer;
        $this->locales = $locales;
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
        $resolver->setDefaults([
            'template_field_name' => 'template',
            'iframe_height' => 500,
            'class' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = $view->vars + array(
                'template_field_name' => $options['template_field_name'],
                'iframe_height' => $options['iframe_height'],
                'class' => $options['class'],
                'locales' => $this->locales
            );
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
