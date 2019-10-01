Introduction
============

This bundles helps you to add a field in your backend and edit all template contents dynamically thanks to [CKEditor inline editing](https://ckeditor.com/docs/ckeditor4/latest/guide/dev_inline.html)

It lets you edit also images by using [ComurImageBundle](https://github.com/comur/ComurImageBundle)

If this bundle helps you reduce time to develop, you can pay me a cup of coffee ;)

[![coffee](Resources/docs/coffee.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2RWFAL3ZNTGN6&source=url)

[![coffee](https://www.paypalobjects.com/en_US/FR/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2RWFAL3ZNTGN6&source=url)


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
    templates_parameter: 'my_templates' # (optional) if you use this parameter, the bundle will use this parameter name as templates parameter and try to get templates using this parameter name
    entity_name: 'page' # (optional) a page parameter is passed to all templates when editing in backend (except if entity is not persisted yet)
    editable_tags: ['li', 'ul', 'td', 'th', 'i', 'span'] # (optional) use this parameter to set editable tags list for CKEditor, see: https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_dtd.html#property-S-editable
    templates: # optional template parameters 
        -   template: 'sections/developers/login.html.twig'
            controller: 'App\Controller\PageController::index' # Use controller instead of template (need anyway a template field to match and get controller parameter)
            controllerParams:
                pageId: '123'
                #...
```

Ex: screenshot of multilingual admin

![Edit Inline page multilingual](Resources/docs/edit-text.png?raw=true)

Fullscreen mode:

![Edit Inline page fullscreen](Resources/docs/edit-fullscreen.png?raw=true)

You can even edit a button label:

![Edit Inline page's button text multilingual](Resources/docs/edit-button.png?raw=true)

### Editable images

Edit images with [ComurImageBundle](https://github.com/comur/ComurImageBundle)

![Editable image](Resources/docs/image-editable.png?raw=true)

Select from your library (already uploaded images)

![Editable image library](Resources/docs/image-lib.png?raw=true)

Upload image

![Editable image upload](Resources/docs/image-upload.png?raw=true)

Crop on the go with HTML attribute defined sizes (crop / resize)

![Editable image crop](Resources/docs/image-crop.png?raw=true)

Usage
=====

## Step 1: Add twig form template

### NOT NEEDED ANYMORE, template is added automatically now !

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
            'required' => true // field extends symfony's HiddenType so you can use options from this class
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

Using ComurImageBundle to edit images
=====================================

## Step 1: Install Comur Image Bundle

Please follow instructions on [ComurImageBundle](https://github.com/comur/ComurImageBundle) to install it

## Step 2: Activate comur_image_compatibility in config

:warning: **NOT NEEDED ANYMORE, bundle checks if ComurImageBundle is installed or not and activates automatically if it finds it**

```yaml
comur_content_admin:
  #...
  enable_comur_image_bundle: true

```

## Step 3: Add data attributes on images of your template

Ex: 

```
<img 
  src="myimage.png" 
  data-content-id="myHeaderImage" 
  data-image-crop-min-width="300" 
  data-image-crop-min-height="500"
  data-image-upload-show-library="true"
  data-image-upload-library-dir="headerimages" <!-- Special parameter : This suffix will be added to uploadRoute and uploadUrl -->
  <!-- You can use (override for each image individually) all ComurImageBundle config parameters by prefixing it 
  with data-image-upload for uploadConfig and data-image-crop for cropConfig (except thumbs parameter) -->
  width="300" height="500" <!-- if you put width and height, ComurImageBundle will ask and crop for exact size of your requirements (forceResize=true) -->
/>
```

This code will automatically replaced by twig filter and src will be automatically filled by database value

This limits usage to img tags (and cannot be used for background images) so if someone has an idea to edit background images, let me know ;)

## Step 4: Use it in your form

```php
use Comur\ContentAdminBundle\Form\InlineContentType;

//...

protected function configureFormFields(FormMapper $formMapper): void
{
    $myEntity = $this->getMyEntity(); // Change it :)

    $formMapper
        //...
        ->add('content', InlineContentType::class, array(
            'template_field_name' => 'template', // this will get template name from another field inside the same form (default is template)
            'template' => 'frontend/index.html.twig', // optional, you must specify either one of template_field_name or template parameter
            'class' => Page::class, // classname of your entity
            'locales' => array('en', 'fr'), // Or pass a parameter (optional, you can globally configure it in yaml and override it here)
            'required' => true // field extends symfony's HiddenType so you can use options from this class too
            'comur_image_params' => array(
                'uploadConfig' => array(
                    'uploadRoute' => 'comur_api_upload', 		//optional
                    'uploadUrl' => $myEntity->getUploadRootDir(),       // required - see explanation below (you can also put just a dir path)
                    'webDir' => $myEntity->getUploadDir(),				// required - see explanation below (you can also put just a dir path)
                    'fileExt' => '*.jpg;*.gif;*.png;*.jpeg', 	//optional
                    'libraryDir' => null, 						//optional
                    'libraryRoute' => 'comur_api_image_library', //optional
                    'showLibrary' => true, 						//optional
                    'saveOriginal' => 'originalImage',			//optional
                    'generateFilename' => true			//optional
                ),
                'cropConfig' => array(
                    'minWidth' => 588,
                    'minHeight' => 300,
                    'aspectRatio' => true, 				//optional
                    'cropRoute' => 'comur_api_crop', 	//optional
                    'forceResize' => false, 			//optional
                    'thumbs' => array( 					//optional
                      array(
                        'maxWidth' => 180,
                        'maxHeight' => 400,
                        'useAsFieldImage' => true  //optional
                      )
                    )
                )
            )
        ))
        //...
    ;
}

```

Please refer to [ComurImageBundle documentation](https://github.com/comur/ComurImageBundle) for complete list of parameters

Development
===========

Any help in improving this bundle is kindly appreciated ! Please do not hesitate to send PR !

TODO
====

- Tests (not realy good at that so if someone wants to help !)
- Add CKEditor parameters in config / form parameters
- Parameter to not include ckeditor script if not needed (if already included elsewhere)
- Find a way to pass parameters to templates (lot of time we have parameters to pass to templates and it's not possible for now)
- ~~Add [ComurImageBundle](https://github.com/comur/ComurImageBundle) compatibility to edit images~~ **DONE**
- Find a better way to replace content with data without using Dom Manipulation (can alter tags)

Troubleshooting
===============

### Content didn't replaced

One reason for that is if you put some extra spaces in your html tags. DomDocument fixes html and removes extra spaces or other things that it founds not respectful of HTML rules.
Ex:

```HTML
<img src="myimage.png"  my-attribute="myvalue" /> <!-- will not be replaced -->
<img src="myimage.png" my-attribute="myvalue" /> <!-- will not be replaced as there is a space at the end before /> -->

<img src="myimage.png" my-attribute="myvalue"/> <!-- this is ok -->

``` 
