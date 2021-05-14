<?php

namespace frontend\controllers;

use app\models\RatingMain;
use Yii;
use app\models\RatingStars;
use app\models\RatingStarsAdditionalKeyValue;
use app\models\RatingType;
use app\models\RatingTypeConfigFields;
use app\models\RatingTypeConfigLookupValues;
use app\models\VAdditionalKeyValueEntries;
use frontend\models\RatingStarsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RatingstarsController implements the CRUD actions for RatingStars model.
 */
class RatingstarsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RatingStars models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RatingStarsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RatingStars model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $modelVAdditionalKeyValueEntries = VAdditionalKeyValueEntries::find()->select(['fieldname', 'value'])->where(["fk_rating_stars_id" => $id])->asArray()->all();
        $additionalFieldsStars = array();
        foreach($modelVAdditionalKeyValueEntries as $key=>$value) { $additionalFieldsStars[$value["fieldname"]] = $value["value"]; }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'additionalFieldsStars' => $additionalFieldsStars,
        ]);
    }

    /**
     * Creates a new RatingStars model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($fk_rating_main_id = -1, $dynPart = 2)
    {
        $model = new RatingStars();
        if ($fk_rating_main_id != -1)
        {
            $model->fk_rating_main_id = $fk_rating_main_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // START DYNAMIC MODEL
            $isDynamicModel = false;
            if (isset(Yii::$app->request->post()["DynamicModel"])) { $isDynamicModel = true; }
            if ($isDynamicModel)
            {
                $postData = Yii::$app->request->post()["DynamicModel"];
                $fk_rating_star_id = $model->id;
                $fk_rating_main_id = $model->fk_rating_main_id;
                $modelRatingMain = RatingMain::findOne($fk_rating_main_id);
                $fk_rating_type_id = $modelRatingMain->fk_rating_type_id;
                $this->saveDynamicModel($postData, $fk_rating_star_id, $fk_rating_main_id, $fk_rating_type_id);
            }
            // END DYNAMIC MODEL

            return $this->redirect(['view', 'id' => $model->id]);
        }

        // DYNAMIC MODEL GENERATE FIELDS START
        $modelRatingMain = RatingMain::findOne($model->fk_rating_main_id);
        $fk_rating_type_id = $modelRatingMain !== null ? $modelRatingMain->fk_rating_type_id : -1;  // RatingMain may not known yet in this scenario

        if ($dynPart == 1) // values are 1 and 2
        {
            $used_4_rating_main = true; 
            $used_4_rating_stars = false;                 
        }
        else
        {
            $used_4_rating_main = false;    
            $used_4_rating_stars = true;                 
        }

        $fieldNamePrefix = $this->get_dyn_attr_fieldname_prefix($used_4_rating_main);
        $fk_rating_type_config_id = $this->get_dyn_attr_fk_rating_type_config_id($fk_rating_type_id);
        $allModelRatingTypeConfigFields = $this->findAllModelRatingTypeConfigFields($fk_rating_type_config_id, $used_4_rating_main, $used_4_rating_stars);
        $fieldnameIds = $this->get_dyn_attr_fieldnameIds($allModelRatingTypeConfigFields);
        $fieldValue = $this->get_dyn_attr_fieldValue($fk_rating_type_config_id, $fieldnameIds);
        // https://www.yiiframework.com/wiki/759/create-form-with-dynamicmodel
        $dynamic_type_config_model = new \yii\base\DynamicModel($fieldnameIds);


        if (Yii::$app->request->isPjax)
        {
            return $this->renderAjax('create', [
                'model' => $model,
    
                'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
                'dynamic_type_config_model' => $dynamic_type_config_model,
                'fieldValue' => $fieldValue,
                'fieldNamePrefix' => $fieldNamePrefix,
            ]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
    
                'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
                'dynamic_type_config_model' => $dynamic_type_config_model,
                'fieldValue' => $fieldValue,
                'fieldNamePrefix' => $fieldNamePrefix,
            ]);
        }

    }



    /* ********************************************************************************* */
    // THIS PART IS REDUNDANT TO THE EXACT SAME FUNCTIONS IN RatingMainController
    /* ********************************************************************************* */
    protected function findAllModelRatingTypeConfigLookupValues($fk_rating_type_config_id, $fk_rating_type_config_fields_id)
    {
        if (($model = RatingTypeConfigLookupValues::findAll([
                     'fk_rating_type_config_id' => $fk_rating_type_config_id
                    //,['IN', 'fk_rating_type_config_fields_id', $fk_rating_type_config_fields_id]
                ])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }    

    private function get_dyn_attr_fieldValue($fk_rating_type_config_id, $fieldnameIds)
    {
        $allModelRatingTypeConfigLookupValues = $this->findAllModelRatingTypeConfigLookupValues($fk_rating_type_config_id, $fieldnameIds);
        $fieldValue = array();
        $fieldValue[null] = null;
		foreach($allModelRatingTypeConfigLookupValues as $allModelRatingTypeConfigLookupValue)
		{
            $fieldValue[$allModelRatingTypeConfigLookupValue->fk_rating_type_config_fields_id][$allModelRatingTypeConfigLookupValue->id] = $allModelRatingTypeConfigLookupValue->value;
		}
        return $fieldValue;
    }

    private function get_dyn_attr_fieldnameIds($allModelRatingTypeConfigFields)
    {
        $fieldnameIds = array();
        $fieldnameIds[null] = null;
		foreach($allModelRatingTypeConfigFields as $allModelRatingTypeConfigField)
		{
            $fieldnameIds[$allModelRatingTypeConfigField->id] = $allModelRatingTypeConfigField->id;
		}
        return $fieldnameIds;
    }

    protected function findAllModelRatingTypeConfigFields($fk_rating_type_config_id, $used_4_rating_main, $used_4_rating_stars, $res_as_array_and_ignore_filter = false)
    {
        // if (($model = RatingTypeConfigFields::findAll([
        //              'fk_rating_type_config_id' => $fk_rating_type_config_id
        //             ,'used_4_rating_main' => $used_4_rating_main
        //             ,'used_4_rating_stars' => $used_4_rating_stars
        //         ])) !== null)
        if (!$res_as_array_and_ignore_filter)
        {
            if (($model = RatingTypeConfigFields::find()
                    ->where
                     ([
                         'fk_rating_type_config_id' => $fk_rating_type_config_id
                        ,'used_4_rating_main' => $used_4_rating_main
                        ,'used_4_rating_stars' => $used_4_rating_stars
                     ])
                    ->orderBy(['id' => SORT_ASC]) // order for fields in form
                    ->all()
                    ) !== null)
            {
                return $model;
            }
        }
        else
        {
            if (($model = RatingTypeConfigFields::find()
                    ->where
                     ([
                         'fk_rating_type_config_id' => $fk_rating_type_config_id
                     ])
                    ->orderBy(['id' => SORT_ASC]) // order for fields in form
                    ->asArray()
                    ->all()
                    ) !== null)
            {
                return $model;
            }   
        }


        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    protected function findModelRatingType($id, $errorTolerant = true)
    {
        $model = RatingType::findOne($id);
        
        if ($model !== null) {
            return $model;
        }
        else
        {
            if (! $errorTolerant)
            {
                throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
            }    
            return $model;
        }
    }    

    private function get_dyn_attr_fk_rating_type_config_id($fk_rating_type_id)
    {
        $returnValue = -1;
        $modelRatingType = $this->findModelRatingType($fk_rating_type_id);
        $fk_rating_type_config_id = -1;
        if ($modelRatingType !== null)
        {
            $returnValue = $modelRatingType->fk_rating_type_config_id;
            return $returnValue;
        }
        return $returnValue;
    }

    private function get_dyn_attr_fieldname_prefix($used_4_rating_main = true)
    {
        return $used_4_rating_main ? "rating_main" : "rating_stars";
    }

    private function is_dyn_attr_main_or_stars($ratingTypeConfigFieldsArray, $id)
    {
        // return 1 for main and 2 for stars
        foreach($ratingTypeConfigFieldsArray as $key=>$element)
        {
            if ($element["id"] == $id)
            {
                return $element["used_4_rating_main"] == true ? 1 : 2;
            }
        }
        return null;
    }    
    
    private function is_dyn_attr_lookup($ratingTypeConfigFieldsArray, $id)
    {
        // return true or false
        foreach($ratingTypeConfigFieldsArray as $key=>$element)
        {
            if ($element["id"] == $id)
            {
                return $element["is_a_lookup_value"];
            }
        }
        return null;
    }    
    private function dyn_attr_datatype($ratingTypeConfigFieldsArray, $id)
    {
        // return can be INT (=> integer), STR (=> string), CUR (=> currency), NUM (=> numeric), BOO (=> boolean)
        foreach($ratingTypeConfigFieldsArray as $key=>$element)
        {
            if ($element["id"] == $id)
            {
                return substr(strtoupper($element["datatype"]),0,3);  
            }
        }
        return null;
    }

    private function saveDynamicModel($postData, $fk_rating_star_id, $fk_rating_main_id, $fk_rating_type_id)
    {
        $fk_rating_type_config_id = $this->get_dyn_attr_fk_rating_type_config_id($fk_rating_type_id);

        $allModelRatingTypeConfigFields = $this->findAllModelRatingTypeConfigFields($fk_rating_type_config_id, $used_4_rating_main = null, $used_4_rating_stars = null, $res_as_array_and_ignore_filter = true);

        // VarDumper::dump($postData); // => Yii::$app->request->post()["DynamicModel"]
        /*
        ^ array:7 [▼
        9 => "16"
        11 => "19"
        16 => "123456"
        17 => "31"
        18 => "34"
        19 => "38"
        20 => "3"
        ]
        */
        // VarDumper::dump($allModelRatingTypeConfigFields);
        /*
        ^ array:9 [▼
        0 => array:12 [▼
            "id" => 9
            "fk_rating_type_config_id" => 2
            "fieldname" => "Anteil Arabica/Robusta %"
            "datatype" => "String"
            "allowed_values_csv" => null
            "description" => "Anteil Arabica/Robusta Bohnen im Kaffee"
            "used_4_rating_main" => true
            "used_4_rating_stars" => false
            "depends_on_id" => null
            "is_a_lookup_value" => true
            "input_is_mandadory" => false
            "user_can_make_new_suggestion" => false
        ]
        1 => array:12 [▶]
        2 => array:12 [▶]
        3 => array:12 [▶]
        4 => array:12 [▼
            "id" => 16
            "fk_rating_type_config_id" => 2
            "fieldname" => "Nr. Kaffeeliste Roland"
            "datatype" => "int"
            "allowed_values_csv" => null
            "description" => null
            "used_4_rating_main" => true
            "used_4_rating_stars" => false
            "depends_on_id" => null
            "is_a_lookup_value" => false
            "input_is_mandadory" => false
            "user_can_make_new_suggestion" => true
        ]
        5 => array:12 [▶]
        6 => array:12 [▶]
        7 => array:12 [▶]
        8 => array:12 [▼
            "id" => 20
            "fk_rating_type_config_id" => 2
            "fieldname" => "Säureempfinden"
            "datatype" => "int"
            "allowed_values_csv" => null
            "description" => "Wie hoch ist die Säureintensität"
            "used_4_rating_main" => false
            "used_4_rating_stars" => true
            "depends_on_id" => null
            "is_a_lookup_value" => false
            "input_is_mandadory" => false
            "user_can_make_new_suggestion" => true
        ]
        ]
        */
        // die();

        foreach ($postData as $key=>$value)
        {
            if ($value !== "") // if no value is submitted, then no further to do...
            {
                if ($this->is_dyn_attr_main_or_stars($allModelRatingTypeConfigFields, $key) == 1)
                {
                    // first try to load (update scenario)

                    $modelRatingMainAdditionalKeyValue = RatingMainAdditionalKeyValue::findOne(
                        [
                            "fk_rating_main_id" => $fk_rating_main_id
                           ,"fk_rating_type_config_fields_id" => $key
                        ]);
                    if ($modelRatingMainAdditionalKeyValue == null)
                    {
                        $modelRatingMainAdditionalKeyValue = new RatingMainAdditionalKeyValue();
                    }
                    $modelRatingMainAdditionalKeyValue->fk_rating_type_config_fields_id = $key;
                    $modelRatingMainAdditionalKeyValue->fk_rating_main_id = $fk_rating_main_id;
                    if ($this->is_dyn_attr_lookup($allModelRatingTypeConfigFields, $key))
                    {
                        $modelRatingMainAdditionalKeyValue->fk_rating_type_config_lookup_values_id = $value;
                    }
                    else
                    {
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "INT") {$modelRatingMainAdditionalKeyValue->value_integer_1 = $value;}
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "STR") {$modelRatingMainAdditionalKeyValue->value_string_1 = $value;}
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "CUR") {$modelRatingMainAdditionalKeyValue->value_currency_1 = $value;}
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "NUM") {$modelRatingMainAdditionalKeyValue->value_numeric_1 = $value;}
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "BOO") {$modelRatingMainAdditionalKeyValue->value_bool_1 = $value;}
                    }
                    $modelRatingMainAdditionalKeyValue->save();
                    unset($modelRatingMainAdditionalKeyValue);
                }
                else // result is 2
                {
                    if ($fk_rating_star_id !== null)
                    {
                        $modelRatingStarsAdditionalKeyValue = RatingStarsAdditionalKeyValue::findOne(
                            [
                                "fk_rating_stars_id" => $fk_rating_star_id
                               ,"fk_rating_type_config_fields_id" => $key
                            ]);
                        if ($modelRatingStarsAdditionalKeyValue == null)
                        {
                            $modelRatingStarsAdditionalKeyValue = new RatingStarsAdditionalKeyValue();
                        }
    
                        $modelRatingStarsAdditionalKeyValue->fk_rating_type_config_fields_id = $key;
                        $modelRatingStarsAdditionalKeyValue->fk_rating_stars_id = $fk_rating_star_id;
                        if ($this->is_dyn_attr_lookup($allModelRatingTypeConfigFields, $key))
                        {
                            $modelRatingStarsAdditionalKeyValue->fk_rating_type_config_lookup_values_id = $value;
                        }
                        else
                        {
                            if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "INT") {$modelRatingStarsAdditionalKeyValue->value_integer_1 = $value;}
                            if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "STR") {$modelRatingStarsAdditionalKeyValue->value_string_1 = $value;}
                            if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "CUR") {$modelRatingStarsAdditionalKeyValue->value_currency_1 = $value;}
                            if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "NUM") {$modelRatingStarsAdditionalKeyValue->value_numeric_1 = $value;}
                            if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $key) == "BOO") {$modelRatingStarsAdditionalKeyValue->value_bool_1 = $value;}
                        }
                        $modelRatingStarsAdditionalKeyValue->save();  
                        unset($modelRatingStarsAdditionalKeyValue);                       
                    }
                }
            }
        }
    }
    /* ********************************************************************************* */
    // THIS PART IS REDUNDANT TO THE EXACT SAME FUNCTIONS IN RatingMainController
    /* ********************************************************************************* */



    /**
     * Updates an existing RatingStars model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $dynPart = 2)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // START DYNAMIC MODEL
            $isDynamicModel = false;
            if (isset(Yii::$app->request->post()["DynamicModel"])) { $isDynamicModel = true; }
            if ($isDynamicModel)
            {
                $postData = Yii::$app->request->post()["DynamicModel"];
                $fk_rating_star_id = $model->id;
                $fk_rating_main_id = $model->fk_rating_main_id;
                $modelRatingMain = RatingMain::findOne($fk_rating_main_id);
                $fk_rating_type_id = $modelRatingMain->fk_rating_type_id;
                $this->saveDynamicModel($postData, $fk_rating_star_id, $fk_rating_main_id, $fk_rating_type_id);
            }
            // END DYNAMIC MODEL


            return $this->redirect(['view', 'id' => $model->id]);
        }

        // DYNAMIC MODEL GENERATE FIELDS START
        $modelRatingMain = RatingMain::findOne($model->fk_rating_main_id);
        // $fk_rating_type_id = $model->fk_rating_type_id;
        $fk_rating_type_id = $modelRatingMain->fk_rating_type_id;


        if ($dynPart == 1) // values are 1 and 2
        {
            $used_4_rating_main = true; 
            $used_4_rating_stars = false;                 
        }
        else
        {
            $used_4_rating_main = false;    
            $used_4_rating_stars = true;                 
        }

        $fieldNamePrefix = $this->get_dyn_attr_fieldname_prefix($used_4_rating_main);
        $fk_rating_type_config_id = $this->get_dyn_attr_fk_rating_type_config_id($fk_rating_type_id);
        $allModelRatingTypeConfigFields = $this->findAllModelRatingTypeConfigFields($fk_rating_type_config_id, $used_4_rating_main, $used_4_rating_stars);
        $fieldnameIds = $this->get_dyn_attr_fieldnameIds($allModelRatingTypeConfigFields);
        $fieldValue = $this->get_dyn_attr_fieldValue($fk_rating_type_config_id, $fieldnameIds);
        // https://www.yiiframework.com/wiki/759/create-form-with-dynamicmodel
        $dynamic_type_config_model = new \yii\base\DynamicModel($fieldnameIds);

        // update DynamicModel attributes with saved values from the database
        $modelRatingStarsAdditionalKeyValue = RatingStarsAdditionalKeyValue::find()->where(["fk_rating_stars_id" => $id])->asArray()->all();
        foreach($dynamic_type_config_model->attributes() as $d_key=>$d_value)
        {
            foreach($modelRatingStarsAdditionalKeyValue as $s_key=>$s_value)
            {
                if ($d_value == $s_value["fk_rating_type_config_fields_id"])
                {

                    if ($this->is_dyn_attr_lookup($allModelRatingTypeConfigFields, $d_value))
                    {
                        $dynamic_type_config_model->setAttributes([$d_value => $s_value["fk_rating_type_config_lookup_values_id"]], false);
                    }
                    else
                    {
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $d_value) == "INT") {$dynamic_type_config_model->setAttributes([$d_value => $s_value["value_integer_1"]], false);}
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $d_value) == "STR") {$dynamic_type_config_model->setAttributes([$d_value => $s_value["value_string_1"]], false);}
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $d_value) == "CUR") {$dynamic_type_config_model->setAttributes([$d_value => $s_value["value_currency_1"]], false);}
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $d_value) == "NUM") {$dynamic_type_config_model->setAttributes([$d_value => $s_value["value_numeric_1"]], false);}
                        if ($this->dyn_attr_datatype($allModelRatingTypeConfigFields, $d_value) == "BOO") {$dynamic_type_config_model->setAttributes([$d_value => $s_value["value_bool_1"]], false);}
                    }
                }
            }
        }
        // DYNAMIC MODEL GENERATE FIELDS END


        return $this->render('update', [
            'model' => $model,

            'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
            'dynamic_type_config_model' => $dynamic_type_config_model,
            'fieldValue' => $fieldValue,
            'fieldNamePrefix' => $fieldNamePrefix,
        ]);
    }

    /**
     * Deletes an existing RatingStars model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the RatingStars model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RatingStars the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RatingStars::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
