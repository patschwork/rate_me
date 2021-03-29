<?php

namespace frontend\controllers;
use Yii;
use yii\helpers\VarDumper;

class ImporterController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if ($posted_values = Yii::$app->request->post() ) {
            // VarDumper::dump($posted_values, $highlight=true);           
            // die();
            $result = \Yii::$app->db->createCommand("SELECT * FROM kaffeeliste_roland_eintrag_uebernehmen(:paramName1)") 
            ->bindValue(':paramName1' , $posted_values['import_nr_txt'] )
            ->execute();

            VarDumper::dump($result, $highlight=true);
            return $this->render('index');
        }
        
        return $this->render('index');
    }

}
