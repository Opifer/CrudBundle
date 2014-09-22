<?php

namespace Opifer\CrudBundle\Tests\TestData;

use Opifer\CrudBundle\Annotation as CRUD;

class User
{
    /**
     * @CRUD\Grid(listable=true)
     */
    protected $name;

    /**
     * @CRUD\Grid(listable=true)
     * @CRUD\Form(editable=true)
     */
    protected $email;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
