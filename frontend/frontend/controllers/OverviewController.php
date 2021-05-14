<?php

namespace frontend\controllers;

use app\models\RatingImages;
use Yii;
use app\models\RatingMain;
use app\models\RatingStars;
use app\models\VAdditionalKeyValueEntries;
use frontend\models\RatingMainSearch;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OverviewController implements the CRUD actions for RatingStars model.
 */
class OverviewController extends Controller
{
    /**
     * Finds the RatingMain model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RatingMain the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RatingMain::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    protected function findModelImages($id)
    {
        if (($model = RatingImages::find()
                ->where(["fk_rating_main_id" => $id])
                ->orderBy(['is_main_picture' => SORT_DESC, 'inserted_dt' => SORT_DESC])
                ->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    protected function findModelStars($id)
    {
        if (($model = RatingStars::find()->where(["fk_rating_main_id" => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    protected function findadditionalFieldsMain($id)
    {
        $modelVAdditionalKeyValueEntries = VAdditionalKeyValueEntries::find()->select(['fieldname', 'value'])->where(["fk_rating_main_id" => $id])->asArray()->all();
        $additionalFieldsMain = array();
        foreach($modelVAdditionalKeyValueEntries as $key=>$value) { $additionalFieldsMain[$value["fieldname"]] = $value["value"]; }
        return $additionalFieldsMain;
    }



    // Test URL: http://patsch3/rate_me_dev/frontend/web/index.php?r=overview/view&id=52
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'model_images' => $this->findModelImages($id),
            'model_stars' => $this->findModelStars($id),
            'additionalFieldsMain' => $this->findadditionalFieldsMain($id),
        ]);
    }
}
