<?php

namespace console\controllers;

use console\components\ReferralManager;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class ReferralController extends Controller
{
    public function actionGetData(int $clientUuid, string $start = null, string $end = null)
    {
        /** @var ReferralManager $referralManager */
        $referralManager = \Yii::$app->referralManager;
        $clientTree = $referralManager->clientTree($clientUuid);
        $volumeAndProfit = $referralManager->getProfitAndVolume($clientUuid, $start, $end);

        $this->stdout("Дерево рефералов: ");
        VarDumper::dump($clientTree);
        $this->stdout("\n");

        $this->stdout("Суммарный объем: " . ArrayHelper::getValue($volumeAndProfit, 'volume', 0) . "\n");
        $this->stdout("Прибыльность: " . ArrayHelper::getValue($volumeAndProfit, 'profit', 0) . "\n");
        $this->stdout("Количество уровней реферальной сетки: " . $referralManager->referralDepth($clientTree) . "\n");
        $this->stdout("Количество прямых рефералов: " . $referralManager->countDirectReferral($clientTree) . "\n");
        $this->stdout("Количество всех рефералов: " . $referralManager->countAllReferral($clientTree) . "\n");
        return ExitCode::OK;
    }
}