<?php

namespace Opifer\CrudBundle\RulesEngine;

use Opifer\CrudBundle\Doctrine\EntityHelper;
use Opifer\CrudBundle\Transformer\EntityTransformer;
use Opifer\RulesEngine\Rule\Condition\Condition;
use Opifer\RulesEngine\Rule\Condition\StringCondition;
use Opifer\RulesEngine\Rule\Condition\ConditionSet;
use Opifer\RulesEngineBundle\Provider\AbstractProvider;
use Opifer\RulesEngineBundle\Provider\ProviderInterface;

class UserRuleProvider extends AbstractProvider implements ProviderInterface
{
    private $entityHelper;
    private $entityTransformer;

    public function __construct(EntityHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }

    public function buildRules()
    {
        $rules = array();
        $rules[] = new \Opifer\RulesEngine\Rule\RuleSet();
        $rules[] = new \Opifer\RulesEngine\Rule\Condition\ConditionSet();

        // @todo Transform
        $entity = $this->context;

        foreach ($this->entityHelper->getProperties($entity) as $property) {

            $className = $this->entityHelper->getMetaData($entity)->getName();
            $className = (false === strpos($className, '\\')) ? $className : substr($className, strrpos($className, '\\') + 1);

            $condition = new StringCondition();
            $condition
                ->setName($className . ': ' . $property['fieldName'])
                ->setEntity($entity)
                ->setAttribute('a.' . $property['fieldName'])
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
