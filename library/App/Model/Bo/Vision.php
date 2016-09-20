<?php

use GoogleCloudVisionPHP\GoogleCloudVision;

class App_Model_Bo_Vision
{
    public function __construct(){
        $config = Zend_Registry::getInstance()->get('config')->get('chave');
        $this->_dao = new GoogleCloudVision();
        $this->_dao->setKey($config->google);
    }

    public function process($fileContents)
    {

        $this->_dao->setImage($fileContents, "RAW");

        // 1 is Max result
//        $this->_dao->addFeature("LABEL_DETECTION", 1);
        $this->_dao->addFeature("TEXT_DETECTION", 200);

//        $this->_dao->addFeatureUnspecified(1);
//        $this->_dao->addFeatureFaceDetection(1);
//        $this->_dao->addFeatureLandmarkDetection(1);
//        $this->_dao->addFeatureLogoDetection(1);
//        $this->_dao->addFeatureLabelDetection(1);
//        $this->_dao->addFeatureOCR(1);
//        $this->_dao->addFeatureSafeSeachDetection(1);
//        $this->_dao->addFeatureImageProperty(1);

        //Optinal
//        $this->_dao->setImageContext(array("languageHints"=>array("th")));

        $response = $this->_dao->request();

        return $response;
    }
}
