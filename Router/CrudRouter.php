<?php

namespace Opifer\CrudBundle\Router;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class CrudRouter extends AbstractRouter implements RouterInterface
{
    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, $prefix = '')
    {
        parent::__construct($container);
        $this->defineRoutes($prefix);
    }

    /**
     * Define all routes in this router
     *
     * @param string $prefix
     *
     * @return void
     */
    public function defineRoutes($prefix = '')
    {
        $routes = array();

        $routes['opifer.crud.index'] = new Route($prefix.'/{slug}', [
            '_controller' => 'OpiferCrudBundle:Crud:index'
        ]);

        $routes['opifer.crud.new'] = new Route($prefix.'/{slug}/new', [
            '_controller' => 'OpiferCrudBundle:Crud:new'
        ]);

        $routes['opifer.crud.edit'] = new Route($prefix.'/{slug}/edit/{id}', [
            '_controller' => 'OpiferCrudBundle:Crud:edit'
        ]);

        $routes['opifer.crud.delete'] = new Route($prefix.'/{slug}/delete/{id}', [
            '_controller' => 'OpiferCrudBundle:Crud:delete'
        ]);

        foreach ($routes as $key => $route) {
            $this->routeCollection->add($key, $route);
        }
    }

    /**
     * Tries to match a URL path with a set of routes.
     *
     * If the matcher can not find information, it must throw one of the
     * exceptions documented below.
     *
     * @param string $pathinfo The path info to be parsed (raw format, i.e. not
     *                         urldecoded)
     *
     * @return array An array of parameters
     *
     * @throws ResourceNotFoundException If the resource could not be found
     */
    public function match($pathinfo)
    {
        $urlMatcher = new UrlMatcher($this->routeCollection, $this->getContext());
        $result = $urlMatcher->match($pathinfo);

        if (!empty($result)) {
            $entity = $this->container->get('opifer.crud.slug_transformer')
                ->transform($result['slug']);

            if (false === $entity) {
                throw new ResourceNotFoundException('The route '.$pathinfo.' is not registered on the CrudBundle.');
            }

            $result['entity'] = $entity;
        }

        return $result;
    }
}
