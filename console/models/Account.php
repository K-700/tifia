<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accounts".
 *
 * @property int $id
 * @property int|null $client_uid
 * @property int|null $login
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accounts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_uid', 'login'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_uid' => 'Client Uid',
            'login' => 'Login',
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this
            ->hasOne(User::class, ['client_uid' => 'client_uid'])
            ->inverseOf('accounts');
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getTrade()
    {
        return $this
            ->hasOne(Trade::class, ['login' => 'login'])
            ->inverseOf('account');
    }
}
