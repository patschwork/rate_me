<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[VAdditionalKeyValueEntries]].
 *
 * @see VAdditionalKeyValueEntries
 */
class VAdditionalKeyValueEntriesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return VAdditionalKeyValueEntries[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return VAdditionalKeyValueEntries|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
