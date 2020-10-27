<?php

namespace console\components;

use app\models\Trade;
use app\models\User;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class ReferralManager extends \yii\base\Component
{
    public $cache;
    /** @var array */
    protected $_clientPartners;

    public function init()
    {
        $this->cache = Instance::ensure($this->cache, 'yii\caching\CacheInterface');

        $this->_clientPartners = $this->cache->getOrSet([__CLASS__, 'tree'], function () {
            // тут нужен большой group_concat_max_len, т.к., в теории, можно упереться в лимит
            $tree = User::find()
                ->select(['partner_id', 'client_uids' => 'GROUP_CONCAT(DISTINCT client_uid)'])
                ->andWhere(['not', ['partner_id' => null]])
                ->indexBy('partner_id')
                ->groupBy('partner_id')
                ->asArray()
                ->all();

            if ($tree) {
                foreach ($tree as $clientId => $clientData) {
                    $tree[$clientId] = array_map('intval', explode(',', $clientData['client_uids']));
                }
            }
            return $tree ?? [];
        });
    }

    protected function getChild(int $clientUuid): array
    {
        $child = [];
        foreach (ArrayHelper::getValue($clientUuid, $this->_clientPartners, []) as $children) {
            $child = array_merge($child, $this->getChild($children));
        }

        return $child;
    }

    protected function getChildIds(int $clientUuid): array
    {
        if ($child = ArrayHelper::getValue($this->_clientPartners, $clientUuid, [])) {
            foreach ($child as $childId) {
                $child = array_merge($child, $this->getChildIds($childId));
            }
        }

        return $child;
    }

    public function clientTree(int $clientUuid): array
    {
        // непонятно, что затратнее (с точки зрения память/производительность) - ложить в кеш или вычислять каждый раз
        $partnerIds = [];
        foreach (ArrayHelper::getValue($this->_clientPartners, $clientUuid, []) as $partnerId) {
            if (isset($this->_clientPartners, $partnerId)) {
                $partnerIds[$partnerId] = $this->clientTree($partnerId, $this->_clientPartners);
            } else {
                $partnerIds[$partnerId] = [];
            }
        }
        return $partnerIds;
    }

    public function referralDepth(array $array): int
    {
        $maxDepth = 0;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::referralDepth($value) + 1;

                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;
    }

    public function countDirectReferral(array $clientTree): int
    {
        return count($clientTree, COUNT_NORMAL);
    }

    public function countAllReferral(array $clientTree): int
    {
        return count($clientTree, COUNT_RECURSIVE);
    }

    public function getProfitAndVolume(int $clientUuid, string $start = null, string $end = null): array
    {
        return Trade::find()
            ->select(['volume' => 'IFNULL(SUM(volume * coeff_h * coeff_cr), 0)', 'profit' => 'IFNULL(SUM(profit), 0)'])
            ->innerJoinWith('account.user', false)
            ->andWhere(['in', User::tableName().'.client_uid', array_merge([$clientUuid], $this->getChildIds($clientUuid))])
            ->andFilterWhere(['>=', 'close_time', $start])
            ->andFilterWhere(['<=', 'close_time', $end])
            ->asArray()
            ->one();
    }
}