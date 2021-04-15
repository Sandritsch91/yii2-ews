<?php
/**
 * @package yii2-ews
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\ews\conditions;

use jamesiarmes\PhpEws\Type\ConstantValueType;
use jamesiarmes\PhpEws\Type\FieldURIOrConstantType;
use jamesiarmes\PhpEws\Type\IsEqualToType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use yii\db\ExpressionInterface;

/**
 * {@inheritDoc}
 */
class HashConditionBuilder extends \yii\db\conditions\HashConditionBuilder
{
    /**
     * @var \simialbi\yii2\ews\QueryBuilder
     */
    protected $queryBuilder;

    /**
     * {@inheritDoc}
     * @return array
     */
    public function build(ExpressionInterface $expression, array &$params = [])
    {
        /** @var \yii\db\conditions\HashCondition $expression */
        $hash = $expression->getHash();

        $elements = [];
        foreach ($hash as $column => $value) {
            $elements[] = [
                'class' => IsEqualToType::class,
                'FieldURI' => [
                    'class' => PathToUnindexedFieldType::class,
                    'FieldURI' => $this->queryBuilder->getUriFromProperty($column)
                ],
                'FieldURIOrConstant' => [
                    'class' => FieldURIOrConstantType::class,
                    'Constant' => [
                        'class' => ConstantValueType::class,
                        'Value' => $value
                    ]
                ]
            ];
        }

        return count($elements) === 1 ? $elements[0] : $this->queryBuilder->buildCondition(['AND', $elements], $params);
    }
}