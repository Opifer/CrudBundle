<?php

namespace Opifer\CrudBundle\RulesEngine;

use Opifer\RulesEngine\Rule\Condition\ConditionSet;
use Opifer\RulesEngine\Rule\Condition\StringValueCondition;
use Opifer\RulesEngine\Rule\RuleSet;
use Opifer\RulesEngineBundle\Provider\AbstractProvider;
use Opifer\RulesEngineBundle\Provider\ProviderInterface;

use Opifer\CrudBundle\Doctrine\EntityHelper;

class RulesProvider extends AbstractProvider implements ProviderInterface
{
    private $entityHelper;

    public function __construct(EntityHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }

    public function buildRules()
    {
        $rules = array();
        $rules[] = new RuleSet();
        $rules[] = new ConditionSet();

        // @todo Transform
        $entity = $this->context;

        foreach ($this->entityHelper->getProperties($entity) as $property) {

            $className = $this->entityHelper->getMetaData($entity)->getName();
            $className = (false === strpos($className, '\\')) ? $className : substr($className, strrpos($className, '\\') + 1);

            $condition = new StringValueCondition();
            $condition
                ->setName($className . ': ' . $property['fieldName'])
                ->setEntity($entity)
                ->setAttribute($property['fieldName'])
            ;

            switch ($property['type']) {
                case 'text':
                case 'string':
                    $operatorOpts = ['equals', 'notequals', 'contains'];
                    break;
                case 'integer':
                    $operatorOpts = ['equals', 'notequals', 'greaterthan', 'lessthan'];
                    break;
            }

            $condition->setOperatorOpts($operatorOpts);
            $condition->setOperator($operatorOpts[0]);

            $rules[] = $condition;
        }

        return $rules;
    }
}
