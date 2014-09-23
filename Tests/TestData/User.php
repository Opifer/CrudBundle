<?php

namespace Opifer\CrudBundle\Tests\TestData;

use Opifer\CrudBundle\Annotation as CRUD;

class User
{
    protected $id;

    /**
     * @CRUD\Grid(listable=true)
     */
    protected $name;

    /**
     * @CRUD\Grid(listable=true)
     * @CRUD\Form(editable=true)
     */
    protected $email;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

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
