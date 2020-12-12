<?php
// @require API credentials
require_once  'core'. DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .  'api.php';

require_once 'Classes' . DIRECTORY_SEPARATOR . 'Employee.php';

$Employee_results = new \APP\Classes\Employee();

$offset   = filter_var($_GET['offset']?? 0 , FILTER_SANITIZE_NUMBER_INT);
$limit      = filter_var($_GET['limit']?? 10, FILTER_SANITIZE_NUMBER_INT);

$Employee_results_array  = json_decode($Employee_results->getEmployeeResult($offset , $limit));

require_once 'template'. DIRECTORY_SEPARATOR. 'homepage.php';