<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RatingTypeConfigLookupValues]].
 *
 * @see RatingTypeConfigLookupValues
 */
class RatingTypeConfigLookupValuesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return RatingTypeConfigLookupValues[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return RatingTypeConfigLookupValues|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
