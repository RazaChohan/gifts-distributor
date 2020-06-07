<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\Category;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $storagePath  = storage_path() . '/data/employees.json';
        //Check if file exists
        if(file_exists($storagePath)) {
            $employees = json_decode(file_get_contents($storagePath), true);
            //If employees are not empty
            $employeeModel = new Employee();
            $categoryModel = new Category();
            if(!empty($employees)) {
                foreach($employees as $employee) {
                    $interests = $employee['interests'];
                    $employeeInterests = [];
                    //Insert interests
                    foreach($interests as $interest) {
                        $employeeInterests[] = $categoryModel->insertCategory($interest);
                    }
                    $employeeModel->insertEmployee($employee['name'], $employeeInterests);
                }
            }
        } else {
            exit('Employees File not found!!');
        }
    }
}
