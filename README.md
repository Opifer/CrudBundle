[![Build Status](https://travis-ci.org/Opifer/CrudBundle.svg)](https://travis-ci.org/Opifer/CrudBundle)

CrudBundle
==========

This bundle is still very much a work in progress, so BC-breaks will happen until
the first stable release. 

Installation
------------

Add the bundle to your `composer.json`

    composer require opifer/crud-bundle dev-master

Register the necessary bundles in `app/AppKernel.php`

```php
public function registerBundles()
{
    // @todo reduce dependencies
    $bundles = array(
        ...
        new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
        new Genemu\Bundle\FormBundle\GenemuFormBundle(),
        new JMS\SerializerBundle\JMSSerializerBundle(),
        new Opifer\CrudBundle\OpiferCrudBundle(),
        new Opifer\RulesEngineBundle\OpiferRulesEngineBundle(),
        new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
        ...
    }
}
```

This bundle ships with two custom routers. To register them, add them to the
`CmfRoutingBundle` config.

```yaml
cmf_routing:
    chain:
        routers_by_id:
            router.default: 100
            opifer.crud.crud_router: 50
            opifer.crud.api_router: 40
```

Configuration
-------------

Update your config file `app/config/config.yml`

```yaml
opifer_crud:
    # Define a route prefix if necessary
    route_prefix: /
    
    # The template to extend from if you want to use the full crud views.
    # The template needs a 'body' block to successfully load the crud pages
    extend_template: AcmeDemoBundle::base.html.twig

    # Note: Changing the key values will break the routes pointing to that entity.
    # Make sure to fix wherever you point to that route.
    routes:
        # The key is the route part, the value is the related entity.
        #route: Entity
        users:  Acme\DemoBundle\Entity\User
```

After defining a route, you can visit the CRUD pages at the following URL's:

- http://localhost/app_dev.php/users
- http://localhost/app_dev.php/users/new
- http://localhost/app_dev.php/users/edit/:id

Restricting attributes in lists
-------------------------------

By default, the list views show all properties in an entity.
This can be restricted by adding annotations to the entity properties.

```php
namespace Acme\DemoBundle\Entity\User;

use Opifer\CrudBundle\Annotation as CRUD;

class User
{
    /**
     * @CRUD\Grid(listable=true)
     */
    protected $username;
}
```

Restricting form fields in edit views
-------------------------------------

By default, the edit views render form fields for all properties.
This can also be restricted by adding annotations to the entity properties.

```php
namespace Acme\DemoBundle\Entity\User;

use Opifer\CrudBundle\Annotation as CRUD;

class User
{
    /**
     * @CRUD\Form(editable=true)
     */
    protected $username;
}
```

Defining a custom form type for properties
------------------------------------------

By default, the CrudBundle looks at the Doctrine types defined on your properties
to generate the form fields. If you would rather like to define a custom form
type, you can pass a formtype in the `Form` annotation.
note: this formtype must be registered as a service and tagged with `form.type`

```php
namespace Acme\DemoBundle\Entity\User;

use Opifer\CrudBundle\Annotation as CRUD;

class User
{
    /**
     * @CRUD\Form(editable=true, type="acme_type")
     */
    protected $username;
}
```

Creating a custom edit view for an entity
-----------------------------------------

The edit views are rendered by the `OpiferCrudBundle:Crud:edit.html.twig` view
out of the box. It simply calls Twig's `{{ form(form) }}` function, which lists
all (specified) form fields.

Sometimes however, you want to create custom edit views for a certain entity.
You can easily do this by adding a `getEditTemplate` method to your entity.

```php
namespace Acme\DemoBundle\Entity\User;

use Opifer\CrudBundle\Annotation as CRUD;

class User
{
    public function getEditTemplate()
    {
        return 'AcmeDemoBundle:User:edit.html.twig';
    }
}
```
