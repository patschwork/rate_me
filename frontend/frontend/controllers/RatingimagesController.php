<?php

namespace frontend\controllers;

use Yii;
use app\models\RatingImages;
use Exception;
use frontend\models\RatingImagesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use yii\web\UploadedFile;
use yii\db\Expression;
use yii\helpers\VarDumper;

/**
 * RatingimagesController implements the CRUD actions for RatingImages model.
 */
class RatingimagesController extends Controller
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
     * Lists all RatingImages models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RatingImagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RatingImages model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
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
            $exif = exif_read_data($filename);
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
          catch(Exception $e)
          {
            // do nothing
          }
        } // if function exists      
      }

    /**
     * Creates a new RatingImages model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($fk_rating_main_id = -1)
    {
        $model = new RatingImages();
        if ($fk_rating_main_id != -1)
        {
            $model->fk_rating_main_id = $fk_rating_main_id;
        }
        $guid=$this->GUIDv4();

 
            if ($model->load(Yii::$app->request->post())){
                    $model->file_blob = UploadedFile::getInstances($model, 'file_blob');
                
                    if ($model->file_blob) {
                        $i=0; // Anzahl Dateien mitzählen...

                        foreach ($model->file_blob as $file) {
                            $i++;
                            if ($i>1)
                            {
                                // Wenn mehrere Dateien hochgeladen werden sollen immer die gleichen Agaben für Namen/Beschreibung verwendet werden
                                $model = new RatingImages();
                                $model->load(Yii::$app->request->post());
                            }
                            try
                            {
                                if (file_exists($file->tempName))
                                {
                                    $this->correctImageOrientation($file->tempName);

                                    // https://stackoverflow.com/questions/22661515/store-image-into-postgresql-bytea-field-using-yiiframework        
                                    $data = pg_escape_bytea(file_get_contents($file->tempName));

                                    $model->file_blob = new Expression("'{$data}'");
                                    
                                    $model->filename = $file->baseName . '.' . $file->extension;
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
                                        $model->is_main_picture=true;    
                                    }
                                    $model->session_upload_key = $guid;

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

                                    $model->image_width  = $image_info[0];
                                    $model->image_height = $image_info[1];
                                    $model->image_type = $image_info[2];
                                    $model->image_htmlimg_width_heigt = $image_info[3];
                                    $model->image_bits = $image_info["bits"];
                                    $model->image_mime = $image_info["mime"];

                                    $model->save();
                                }
                                else {
                                    $model_Err_1 = new RatingImages();
                                    $model_Err_1->fk_rating_main_id = $model->fk_rating_main_id;
                                    $model_Err_1->filename = $file->baseName . '.' . $file->extension;
                                    $model_Err_1->description = "ERROR ON UPLOAD. No file found!";
                                    $model_Err_1->session_upload_key = $guid;
                                    $model_Err_1->save(false);                                    
                                }
                            }
                            catch (exception $e)
                            {
                                // Bei Fehler dies wegschreiben
                                $model_Err_2 = new RatingImages();
                                $model_Err_2->fk_rating_main_id = $model->fk_rating_main_id;
                                $model_Err_2->filename = $file->baseName . '.' . $file->extension;
                                $model_Err_2->description = "ERROR ON UPLOAD. Exception: $e";
                                $model_Err_2->session_upload_key = $guid;
                                $model_Err_2->save(false);
                            }
                        } 
                    return $this->redirect(['index', 'RatingImagesSearch[fk_rating_main_id]' => $model->fk_rating_main_id]);
                }
                else
                {
                    $model->save();
                    return $this->redirect(['index', 'RatingImagesSearch[fk_rating_main_id]' => $model->fk_rating_main_id]);
                }
            }
            else
            {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
    }


    /**
     * Updates an existing RatingImages model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_file = $model->file_blob;
        if ($model->load(Yii::$app->request->post())) {
            $model->file_blob = $old_file;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing RatingImages model.
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
     * Finds the RatingImages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RatingImages the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RatingImages::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
