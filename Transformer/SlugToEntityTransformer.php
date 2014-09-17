<?php

namespace Opifer\CrudBundle\Transformer;

/**
 * This class helps transforming strings, possibly passed in a URL or as POST variable,
 * into the matching repository/entity/document
 */
class SlugToEntityTransformer
{
    protected $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function transform($slug)
    {
        foreach ($this->routes as $route => $entity) {
            if ($route === $slug) {
                return new $entity();
            }
        }

        return false;
    }
}
