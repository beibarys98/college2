<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Participant]].
 *
 * @see \common\models\Participant
 */
class ParticipantQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\Participant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\Participant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
