<?php

namespace Opifer\CrudBundle\Router;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class ApiRouter extends AbstractRouter implements RouterInterface
{
    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->defineRoutes();
    }

    /**
     * Define all routes in this router
     *
     * @param string $prefix
     *
     * @return void
     */
    public function defineRoutes()
    {
        $routes = array();

        // Index
        $routes['opifer.crud.api.index'] = new Route('/api/{slug}', [
            '_controller' => 'OpiferCrudBundle:Api:index'
        ]);
        $routes['opifer.crud.api.index']->setMethods(['GET']);

        // Create
        $routes['opifer.crud.api.create'] = new Route('/api/{slug}', [
            '_controller' => 'OpiferCrudBundle:Api:create'
        ]);
        $routes['opifer.crud.api.create']->setMethods(['POST']);

        // View
        $routes['opifer.crud.api.view'] = new Route('/api/{slug}/{id}', [
            '_controller' => 'OpiferCrudBundle:Api:view'
        ]);
        $routes['opifer.crud.api.view']->setMethods(['GET']);

        // Update
        $routes['opifer.crud.api.update'] = new Route('/api/{slug}/{id}', [
            '_controller' => 'OpiferCrudBundle:Api:update'
        ]);
        $routes['opifer.crud.api.update']->setMethods(['PUT']);

        // Delete
        $routes['opifer.crud.api.delete'] = new Route('/api/{slug}/{id}', [
            '_controller' => 'OpiferCrudBundle:Api:delete'
        ]);
        $routes['opifer.crud.api.delete']->setMethods(['DELETE']);

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
            $result['entity'] = $this->container->get('opifer.crud.slug_to_entity_transformer')
                ->transform($result['slug']);

            if (!empty($result['id'])) {
                $result['entity'] = $this->container->get('doctrine')->getRepository(get_class($result['entity']))
                    ->find($result['id']);
            }

            if (false === $result['entity']) {
                throw new ResourceNotFoundException('The route '.$pathinfo.' is not registered on the CrudBundle.');
            }
        }

        return $result;
    }
}
