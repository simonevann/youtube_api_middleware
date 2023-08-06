<?php
//import the Data model
require_once('models/Data.php');

class DataController {

    function getData($id) {
        $data = new Data();
        $data = $data->get($id);
        return $data;
    }

}