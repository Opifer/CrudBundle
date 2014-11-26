<?php

namespace Opifer\CrudBundle\Transformer;

use Opifer\CrudBundle\Exception\IncorrectRouteConfigException;

/**
 * Slug transformer
 * 
 * This class helps transforming strings, possibly passed in a URL or as POST variable,
 * into the matching repository/entity/document
 *
 * @author Rick van Laarhoven <r.vanlaarhoven@opifer.nl>
 */
class SlugTransformer
{
    /** @var array */
    protected $routes;

    /**
     * Constructor
     *
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Transform the slug to its corresponding entity
     *
     * @param string $slug
     *
     * @return object
     */
    public function transform($slug)
    {
        $object = $this->routes[$slug];

        if (!class_exists($object)) {
            throw new IncorrectRouteConfigException();
        }

        return new $object();
    }
}
