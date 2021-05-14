<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RatingMainAdditionalKeyValue]].
 *
 * @see RatingMainAdditionalKeyValue
 */
class RatingMainAdditionalKeyValueQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return RatingMainAdditionalKeyValue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return RatingMainAdditionalKeyValue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
