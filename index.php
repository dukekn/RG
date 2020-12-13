<?php
use APP\Classes\Employee;

// @require API credentials
require_once 'core' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'api.php';

require_once 'Classes' . DIRECTORY_SEPARATOR . 'Employee.php';

$employee_results = new Employee();

$offset = filter_var($_GET['offset'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

$employee_results_array = json_decode($employee_results->getEmployeeResult($offset));

$employee_count   = $employee_results_array->count ?? null;
$employee_list          = $employee_results_array->list ?? null;

require_once 'template' . DIRECTORY_SEPARATOR . 'homepage.php';