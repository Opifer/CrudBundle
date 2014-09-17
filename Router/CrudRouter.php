<?php

namespace Opifer\CrudBundle\Router;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class CrudRouter implements RouterInterface
{
    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    protected $context;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routeCollection;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct($container, $prefix = '')
    {
        $this->container = $container;
        $this->routeCollection = new RouteCollection();
        $this->defineRoutes($prefix);
    }

    /**
     * Define all routes in this router
     *
     * @param  string $prefix
     *
     * @return void
     */
    public function defineRoutes($prefix = '')
    {
        $this->routeCollection->add('opifer.crud.index', new Route($prefix.'/{slug}', [
            '_controller' => 'OpiferCrudBundle:Crud:index'
        ]));

        $this->routeCollection->add('opifer.crud.filter', new Route($prefix.'/{slug}/rows/{rowfilter}/columns/{columnfilter}', [
            '_controller'  => 'OpiferCrudBundle:Crud:index',
            'rowfilter'    => 'default',
            'columnfilter' => 'default'
        ]));

        $this->routeCollection->add('opifer.crud.new', new Route($prefix.'/{slug}/new', [
            '_controller' => 'OpiferCrudBundle:Crud:new'
        ]));

        $this->routeCollection->add('opifer.crud.edit', new Route($prefix.'/{slug}/edit/{id}', [
            '_controller' => 'OpiferCrudBundle:Crud:edit'
        ]));

        $this->routeCollection->add('opifer.crud.delete', new Route($prefix.'/{slug}/delete/{id}', [
            '_controller' => 'OpiferCrudBundle:Crud:delete'
        ]));
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
            $entity = $this->container->get('opifer.crud.slug_to_entity_transformer')
                ->transform($result['slug']);

            if (false === $entity) {
                throw new ResourceNotFoundException('The route '.$pathinfo.' is not registered on the CrudBundle.');
            }

            $result['entity'] = $entity;
        }

        return $result;
    }
    /**
     * Generate an url for a supplied route
     *
     * @param string $name       The path
     * @param array  $parameters The route parameters
     * @param bool   $absolute   Absolute url or not
     *
     * @return null|string
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        $this->urlGenerator = new UrlGenerator($this->routeCollection, $this->context);

        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }

    /**
     * Sets the request context.
     *
     * @param RequestContext $context The context
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * Gets the request context.
     *
     * @return RequestContext The context
     */
    public function getContext()
    {
        if (!isset($this->context)) {
            $request = $this->container->get('request');

            $this->context = new RequestContext();
            $this->context->fromRequest($request);
        }

        return $this->context;
    }

    /**
     * Getter for routeCollection
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        return $this->routeCollection;
    }
}
