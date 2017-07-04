<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once "assets/PHPExcel.php";
require_once 'assets/PHPExcel/IOFactory.php';
require_once 'assets/PHPExcel/Writer/Excel2007.php';
 
class excel extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }
}