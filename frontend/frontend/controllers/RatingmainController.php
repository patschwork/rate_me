<?php

namespace frontend\controllers;

use app\models\RatingImages;
use Yii;
use app\models\RatingMain;
use app\models\RatingStars;
use app\models\RatingType;
use app\models\RatingTypeConfigFields;
use app\models\RatingTypeConfigLookupValues;
use frontend\models\RatingMainSearch;
use Symfony\Component\VarDumper\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\web\UploadedFile;
use yii\db\Expression;
use yii\helpers\VarDumper as HelpersVarDumper;
use yii\web\Response;

use app\models\RatingMainAdditionalKeyValue;
use app\models\RatingStarsAdditionalKeyValue;
use app\models\VAdditionalKeyValueEntries;

/**
 * RatingmainController implements the CRUD actions for RatingMain model.
 */
class RatingmainController extends Controller
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
     * Lists all RatingMain models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RatingMainSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RatingMain model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $modelVAdditionalKeyValueEntries = VAdditionalKeyValueEntries::find()->select(['fieldname', 'value'])->where(["fk_rating_main_id" => $id])->asArray()->all();
        $additionalFieldsMain = array();
        foreach($modelVAdditionalKeyValueEntries as $key=>$value) { $additionalFieldsMain[$value["fieldname"]] = $value["value"]; }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'additionalFieldsMain' => $additionalFieldsMain,
        ]);
    }


    /**
    * Returns a GUIDv4 string
    *
    * Uses the best cryptographically secure method
    * for all supported pltforms with fallback to an older,
    * less secure version.
    *
    * @param bool $trim
    * @return string
    */
    private function GUIDv4 ($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                substr($charid,  0,  8).$hyphen.
                substr($charid,  8,  4).$hyphen.
                substr($charid, 12,  4).$hyphen.
                substr($charid, 16,  4).$hyphen.
                substr($charid, 20, 12).
                $rbrace;
        return $guidv4;
    }    
    
    private function correctImageOrientation($filename) {

        if (function_exists('exif_read_data')) {
          try
          {
            $exif = @exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
              $orientation = $exif['Orientation'];
              if($orientation != 1){
                $img = imagecreatefromjpeg($filename);
                $deg = 0;
                switch ($orientation) {
                  case 3:
                    $deg = 180;
                    break;
                  case 6:
                    $deg = 270;
                    break;
                  case 8:
                    $deg = 90;
                    break;
                }
                if ($deg) {
                  $img = imagerotate($img, $deg, 0);        
                }
                // then rewrite the rotated image back to the disk as $filename 
                imagejpeg($img, $filename, 95);
              } // if there is some rotation necessary
            } // if have the exif orientation info
          }
          catch (Exception $e)
          {
              $dummy = 1;
          }
        } // if function exists      
      }

    /**
     * Creates a new RatingMain model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($fk_rating_type_id = -1, $dynPart = 1)
    {
        $model = new RatingMain();
        $model_images = new RatingImages();
        $model_stars = new RatingStars();

        $guid=$this->GUIDv4();

        if ($model->load(Yii::$app->request->post())) {

            $isDynamicModel = false;
            if (isset(Yii::$app->request->post()["DynamicModel"]))
            {
                // ok, we have a DynamicModel, because there are entries in the table type_config and rating_type_config_fields.
                // Those values are "connected" for the rating_type (fk_rating_type_config_id), which the user selects in the UI.
                // Some values are only for the combination with "rating_main" and the other are for only for the "rating_stars" (the ratings of the indivial users)
                $isDynamicModel = true;
            }

            $model->session_upload_key = $guid;
            $model->save();

            $model_stars->load(Yii::$app->request->post());
            $model_stars->fk_rating_main_id = $model->id;
            $model_stars->fk_user_id = Yii::$app->user->id;
            $model_stars->save();

            // START PICTURE HANDLING
            if ($model_images->load(Yii::$app->request->post())){
                $model_images->file_blob = UploadedFile::getInstances($model_images, 'file_blob');

                if ($model_images->file_blob) {
                    $i=0; // Anzahl Dateien mitzählen...
                    foreach ($model_images->file_blob as $file) {
                        $i++;
                        if ($i>1)
                        {
                            // Wenn mehrere Dateien hochgeladen werden sollen immer die gleichen Agaben für Namen/Beschreibung verwendet werden
                            $model_images = new RatingImages();
                            $model_images->load(Yii::$app->request->post());
                        }
                        try
                        {
                            if (file_exists($file->tempName))
                            {
                                $this->correctImageOrientation($file->tempName);

                                // https://stackoverflow.com/questions/22661515/store-image-into-postgresql-bytea-field-using-yiiframework        
                                $data = pg_escape_bytea(file_get_contents($file->tempName));
                                $model_images->file_blob = new Expression("'{$data}'");
                                
                                $model_images->filename = $file->baseName . '.' . $file->extension;
                                $path = 'uploads/images/' . $file->baseName . '.' . $file->extension;
                                $count = 0;
                                {
                                    while(file_exists($path)) {
                                    $path = 'uploads/images/' . $file->baseName . '_'.$count.'.' . $file->extension;
                                    $count++;
                                    }
                                }
                                $file->saveAs($path);
                                $files[] = $path;
                                // Wenn er das erste Bild ist, dann mache dies per Definition zum "Hauptbild"
                                if ($i==1)
                                {
                                    $model_images->is_main_picture=true;    
                                }
                                else
                                {
                                    $model_images->is_main_picture=false;    
                                }
                                $model_images->session_upload_key = $guid;

                                // Bildgröße extrahieren und in Datenbankspeichern
                                $image_info = getimagesize($path);
                                /*
                                Array ( [0] => 667 
                                        [1] => 184 
                                        [2] => 3 
                                        [3] => width="667" height="184" 
                                        [bits] => 8 
                                        [mime] => image/png )
                                */

                                $model_images->image_width  = $image_info[0];
                                $model_images->image_height = $image_info[1];
                                $model_images->image_type = $image_info[2];
                                $model_images->image_htmlimg_width_heigt = $image_info[3];
                                $model_images->image_bits = $image_info["bits"];
                                $model_images->image_mime = $image_info["mime"];
                                
                                $model_images->fk_rating_main_id = $model->id;
                                
                                $model_images->save();
                            }
                            else {
                                $model_Err_1 = new RatingImages();
                                $model_Err_1->fk_rating_main_id = $model->id;
                                $model_Err_1->filename = $file->baseName . '.' . $file->extension;
                                $model_Err_1->description = "ERROR ON UPLOAD. No file found!";
                                $model_Err_1->session_upload_key = $guid;
                                $model_Err_1->save(false);                                    
                            }
                        }
                        catch (Exception $e)
                        {
                            // Bei Fehler dies wegschreiben
                            $model_Err_2 = new RatingImages();
                            $model_Err_2->fk_rating_main_id = $model->id;
                            $model_Err_2->filename = $file->baseName . '.' . $file->extension;
                            $model_Err_2->description = "ERROR ON UPLOAD. Exception: $e";
                            $model_Err_2->session_upload_key = $guid;
                            $model_Err_2->save(false);
                        }
                    } 
                }
            else
            {
            }
            }
            // END PICTURE HANDLING

            // START DYNAMIC MODEL
            if ($isDynamicModel)
            {
                $postData = Yii::$app->request->post()["DynamicModel"];
                $fk_rating_star_id = null;
                if ($model_stars !== null) { $fk_rating_star_id = $model_stars->id; }
                $fk_rating_main_id = $model->id;
                $fk_rating_type_id = $model->fk_rating_type_id;
                $this->saveDynamicModel($postData, $fk_rating_star_id, $fk_rating_main_id, $fk_rating_type_id);
            }
            // END DYNAMIC MODEL


            return $this->redirect(['view', 'id' => $model->id]);
        }

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
                'model'        => $model,
                'model_images' => $model_images,
                'model_stars'  => $model_stars,
                'ratingTypes'  => $this->getRatingTypes_4_dropdown(),
    
                'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
                'dynamic_type_config_model' => $dynamic_type_config_model,
                'fieldValue' => $fieldValue,
                'fieldNamePrefix' => $fieldNamePrefix,
            ]);

        }
        else
        {
            return $this->render('create', [
                'model'        => $model,
                'model_images' => $model_images,
                'model_stars'  => $model_stars,
                'ratingTypes'  => $this->getRatingTypes_4_dropdown(),
    
                'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
                'dynamic_type_config_model' => $dynamic_type_config_model,
                'fieldValue' => $fieldValue,
                'fieldNamePrefix' => $fieldNamePrefix,
            ]);
    
        }

        
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

    /**
     * Updates an existing RatingMain model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $dynPart = 1)
    {
        $model = $this->findModel($id);
        $model_images = new RatingImages();
        $model_stars = new RatingStars();
        
        if ($model->load(Yii::$app->request->post()) ) {

            $isDynamicModel = false;
            if (isset(Yii::$app->request->post()["DynamicModel"])) { $isDynamicModel = true; }
            
            $model->save();

            // START DYNAMIC MODEL
            if ($isDynamicModel)
            {
                $postData = Yii::$app->request->post()["DynamicModel"];
                $fk_rating_star_id = null;
                $fk_rating_main_id = $model->id;
                $fk_rating_type_id = $model->fk_rating_type_id;
                $this->saveDynamicModel($postData, $fk_rating_star_id, $fk_rating_main_id, $fk_rating_type_id);
            }
            // END DYNAMIC MODEL

            return $this->redirect(['view', 'id' => $model->id]);
        }

        $fk_rating_type_id = $model->fk_rating_type_id;

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
        $modelRatingMainAdditionalKeyValue = RatingMainAdditionalKeyValue::find()->where(["fk_rating_main_id" => $id])->asArray()->all();
        foreach($dynamic_type_config_model->attributes() as $d_key=>$d_value)
        {
            foreach($modelRatingMainAdditionalKeyValue as $s_key=>$s_value)
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
        
        return $this->render('update', [
            'model' => $model,
            'model_images' => $model_images,
            'model_stars'  => $model_stars,
            'ratingTypes'  => $this->getRatingTypes_4_dropdown(),


            'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
            'dynamic_type_config_model' => $dynamic_type_config_model,
            'fieldValue' => $fieldValue,
            'fieldNamePrefix' => $fieldNamePrefix,
        ]);
    }

    /**
     * Deletes an existing RatingMain model.
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

    protected function findAllModelRatingType()
    {
        if (($model = RatingType::find()->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    protected function getRatingTypes_4_dropdown()
    {
        $rating_types = $this->findAllModelRatingType();
        $rating_typesList = array();
        $rating_typesList[null] = null;
		foreach($rating_types as $rating_type)
		{
			$rating_typesList[$rating_type->id] = $rating_type->name;
		}
        return $rating_typesList;      
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

    public function actionConfigfields($id)
    {
        $used_4_rating_main = true;
        $used_4_rating_stars = false;

        return $this->renderTypeConfigDynForms($id, $used_4_rating_main, $used_4_rating_stars);
    }

    public function actionConfigfieldsstars($id)
    {
        $used_4_rating_main = false;
        $used_4_rating_stars = true;

        return $this->renderTypeConfigDynForms($id, $used_4_rating_main, $used_4_rating_stars);
    }

    public function renderTypeConfigDynForms($id, $used_4_rating_main, $used_4_rating_stars)
    {
        // $fieldNamePrefix = $used_4_rating_main ? "rating_main" : "rating_stars";

        // $modelRatingType = $this->findModelRatingType($id);
        // $allModelRatingTypeConfigFields = $this->findAllModelRatingTypeConfigFields($modelRatingType->fk_rating_type_config_id, $used_4_rating_main, $used_4_rating_stars);
        
        // $fieldnameIds = array();
        // $fieldnameIds[null] = null;
		// foreach($allModelRatingTypeConfigFields as $allModelRatingTypeConfigField)
		// {
        //     $fieldnameIds[$allModelRatingTypeConfigField->id] = $allModelRatingTypeConfigField->id;
		// }

        // $allModelRatingTypeConfigLookupValues = $this->findAllModelRatingTypeConfigLookupValues($modelRatingType->fk_rating_type_config_id, $fieldnameIds);

        // $fieldValue = array();
        // $fieldValue[null] = null;
		// foreach($allModelRatingTypeConfigLookupValues as $allModelRatingTypeConfigLookupValue)
		// {
        //     $fieldValue[$allModelRatingTypeConfigLookupValue->fk_rating_type_config_fields_id][$allModelRatingTypeConfigLookupValue->id] = $allModelRatingTypeConfigLookupValue->value;
		// }

        // // https://www.yiiframework.com/wiki/759/create-form-with-dynamicmodel
        // $dynamic_type_config_model = new \yii\base\DynamicModel($fieldnameIds);

        // // return $this->renderPartial('typeconfig', [
        // // with SELECT2 renderAjax must be used... (https://github.com/kartik-v/yii2-widget-select2/issues/19#issuecomment-80413220)
        // return $this->renderAjax('create', [
        //     'id' => $id,
        //     'allModelRatingTypeConfigFields' => $allModelRatingTypeConfigFields,
        //     'dynamic_type_config_model' => $dynamic_type_config_model,
        //     'fieldValue' => $fieldValue,
        //     'fieldNamePrefix' => $fieldNamePrefix,
        // ]);
    }

    public function actionTypeconfigdependent() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (isset(Yii::$app->request->post()['depdrop_parents'])) {
            $parents = Yii::$app->request->post('depdrop_parents');
            if ($parents != null) {
                $parent_id = $parents[0];

                $model = RatingTypeConfigLookupValues::find()->where([
                        'parent_id' => $parent_id
                    ]);
               
                $data = $model->select(['id', 'value AS name'])->asArray()->all(); // IT HAS TO BE "id" AND "name"!

                return [
                    'output' => $data,
                    'selected' => '',
                ];
            }
        }
        return ['output' => '', 'selected' => ''];
    }
    
}
