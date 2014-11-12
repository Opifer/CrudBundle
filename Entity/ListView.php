<?php

namespace Opifer\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\CrudBundle\Model\ListView as BaseListView;

/**
 * @ORM\Table(name="list_view")
 * @ORM\Entity()
 */
class ListView extends BaseListView
{

}
