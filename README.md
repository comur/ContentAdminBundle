Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require comur/content-admin-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require comur/content-admin-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Comur\ContentAdminBundle\ComurContentAdminBundle(),
        ];

        // ...
    }

    // ...
}
```

Configuration
=============

You can configure following parameters by creating a comur_content_admin.yaml in your config directory:

```yaml
#config/comur_content_admin.yaml

comur_content_admin:
    locales: ['en'] # this will add as many translation tabs as the number of locales when rendering content editing field 
```

Ex: screenshot of multilingual admin

![Edit Inline page multilingual](/Resources/docs/edit-text?raw=true "Edit multilingual text")

Fullscreen mode:

![Edit Inline page fullscreen](/Resources/docs/edit-fullscreen?raw=true "Edit fullscreen text")

You can even edit a button label:

![Edit Inline page's button text multilingual](/Resources/docs/edit-button?raw=true "Edit button text")

Usage
=====

## Step 1: Add twig form template

Add form field template to your twig configuration (config/twig.yaml):

```yaml
twig:
#...
    form_themes:
        - '@ComurContentAdmin/inline_content_field.html.twig'

```

## Step 2: Include routes

Include routes in your routes.yaml (config/routes.yaml):

```yaml
comur_content_admin:
    resource: "@ComurContentAdminBundle/Resources/config/routes.yaml"
```

## Step 3: Implement abstract entity

ComurContentAdminBundle uses an property (orm column) to save dynamic content data in database using your related entity.
To do so, you need to extend AbstractInlineContent class. This class will add a property called content in your entity (*so be careful to not use this column in your entity !*).

```php
use Comur\ContentAdminBundle\Entity\AbstractInlineContent;

class MyContentEntity extends AbstractInlineContent {
    //...
}
```

## Step 4: Use it in your admin (ex for sonata admin but can be used in custom admins too)

```php
use Comur\ContentAdminBundle\Form\InlineContentType;

//...

protected function configureFormFields(FormMapper $formMapper): void
{
    $formMapper
        //...
        ->add('content', InlineContentType::class, array(
            'template_field_name' => 'template', // this will get template name from another field inside the same form (default is template)
            'template' => 'frontend/index.html.twig', // optional, you must specify either one of template_field_name or template parameter
            'class' => Page::class, // classname of your entity
            'locales' => array('en', 'fr'), // Or pass a parameter (optional, you can globally configure it in yaml and override it here)
            'required' => true // field extends symfony's HiddenType so you can use options from this class too
        ))
        //...
    ;
}

```

## Step 5: Post size limit

Be careful to your data size limit sent in POST request. You may have to change it to be able to get all from data (all dynamic content values).

For more info check https://www.php.net/manual/en/ini.core.php#ini.post-max-size

Do not forget to check your server's limit too (Nginx, Apache...) 

## Step 6: Add twig filter into the template

This bundle adds a new twig filter called "inlinecontent". You must use it for the bundle to determine and replace contents using data-content-id attribute on html tags needing content replacement.

Ex:

```twig
{# templates/index.html.twig #}

{% extends 'base.html.twig %}

{% block body %}

  {# Need to pass content data to this filter so you need to pass content to your template (see step 7) #}
  {% filter inlinecontent(content) %}
  
    {# ... #}
    <div class="myclass" data-content-id="myCenterBlockDescription">This is my default content that I can replace as I want using my form</div>
  
  {% endfilter %}

{% endblock body%}

```

## Step 7: Pass content to your template from your controller

Content is managed by this bundle but you need to pass your content data to template for the bundle to have it and replace content accordingly.

Ex:

```php
// App/Controller/FrontController.php

namespace App\Controller;

use App\Entity\Page; // This is my entity extending ComurContentAdmin's AbstractInlineContent entity
use Symfony\Component\HttpFoundation\Request; // Handle the request in the controller
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{

  public function index(Request $request, $slug) {
    $page = $this->getDoctrine()->getManager()->getRepository(Page::class)->findOneBySlug($slug);
    return $this->render('frontend/index.html.twig', array(
        'content' => $page->getContent($request->getLocale()) // This will return localized content data as an array and twig filter will replace default content of your template with this
    ));
  }

}

```
