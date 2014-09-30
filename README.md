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

Note: An alternative way is using the standalone `datagrid_builder` in your custom
controller to define the columns in your list view, like explained in the next
chapter.

Using the Datagrid Builder
--------------------------

When using the datagrid builder in your own custom controller, it could look like
something like the following

```php
namespace Acme\DemoBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Acme\DemoBundle\Entity\User;

class UserController extends Controller
{
    /**
     * Index
     *
     * @Route(
     *     "/users",
     *     name="admin.users"
     * )
     *
     * @param  Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $datagrid = $this->get('opifer.crud.datagrid_builder')->create(new User)
            ->addColumn('username', 'text', ['label' => 'User name'])
            ->addColumn('email', 'text', ['label' => 'Email address'])
            ->build()
        ;

        $query = array_merge($request->query->all(), ['slug' => 'users']);

        return $this->render('AcmeDemoBundle:User:index.html.twig', [
            'grid'  => $datagrid,
            'query' => $query
        ]);
    }
}
```

Then, your `index.html.twig` could look something like this:

```twig
{% extends 'AcmeDemoBundle::base.html.twig' %}

{% block body %}

    {% if grid.rows %}
        <section class="row">
            {{ include('OpiferCrudBundle:Pagination:indicator.html.twig', {'pagination': grid.paginator}) }}
            
            {{ include('OpiferCrudBundle:Pagination:paginator.html.twig', {'pagination': grid.paginator, 'query': query}) }}
        </section>

        <section class="row">
            <div class="col-xs-12">
                {{ include('OpiferCrudBundle:Datagrid:table.html.twig') }}
            </div>
        </section>
    {% else %}
        <div class="alert alert-warning">No results found</div>
    {% endif %}

{% endblock %}
```

Passing a closure to generate custom list cell data
---------------------------------------------------

In case you want to change the default behaviour of a row cell value, you could
pass a closure to the `addColumn`'s options array like explained below:

```php
$datagrid = $this->get('opifer.crud.datagrid_builder')->create(new User)
    ->addColumn('username', 'text', [
        'label'    => 'User name',
        'function' => function($value) {
            // The passed $value is the value returned by the getter, matching the
            // first addColumn() parameter. In this case getUsername().
            return strtoupper($value);
        }
    ])
    ->build()
;
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
