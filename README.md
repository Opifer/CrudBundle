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

Using dynamic crud routing & views
----------------------------------

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

Update your config file `app/config/config.yml`

```yaml
opifer_crud:
    # Define a route prefix if necessary
    # Defaults to '/'
    route_prefix: /admin
    
    # The template to extend from if you want to use the full crud views.
    # The template needs a 'body' block to successfully load the crud pages
    # Defaults to "OpiferCrudBundle::base.html.twig"
    extend_template: AcmeDemoBundle::base.html.twig

    # Note: Changing the key values will break the routes pointing to that entity.
    # Make sure to fix wherever you point to that route.
    routes:
        # The key is the route part, the value is the related entity.
        users:  Acme\DemoBundle\Entity\User
```

After defining a route, you can visit the CRUD pages at the following URL's:

- http://localhost/app_dev.php/users
- http://localhost/app_dev.php/users/new
- http://localhost/app_dev.php/users/edit/:id

Documentation
-------------

[Documentation](Resources/doc/index.md)
