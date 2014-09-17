<?php

namespace Opifer\CrudBundle\Templating;

class TwigExtension extends \Twig_Extension
{
    /**
     * Register all filters
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('plainrolename', [$this, 'plainRoleName']),
        ];
    }

    /**
     * Transforms the role name to a more human readable name
     *
     * @param string $value
     *
     * @return string
     */
    public function plainRoleName($value)
    {
        $value = str_replace('_', ' ', str_replace('ROLE_', '', $value));

        return ucfirst(strtolower($value));
    }

    /**
     * Required getName method
     *
     * @return string
     *
     * @todo  rename this service
     */
    public function getName()
    {
        return 'opifer.crud.twig.datagrid_extension';
    }
}
