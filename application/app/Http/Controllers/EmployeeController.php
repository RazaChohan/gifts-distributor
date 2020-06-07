<?php


namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Gift;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class EmployeeController extends Controller
{
    /**
     * Get a suitable gift for employee
     *
     * @param $request
     *
     * @return mixed
     * @throws Exception
     */
    public function getGift(Request $request)
    {
        $response = [];
        $responseCode = null;
        try {
            $employeeID = $request->get('employee_id', 0);
            if ($employeeID > 0) {
                //Get employee
                $employeeModel = new Employee();
                $giftModel = new Gift();
                $employee = $employeeModel->getEmployeeByID($employeeID);
                if(!is_null($employee)) {
                    if(count($employee->gift) === 0) {
                        $giftID = $giftModel->getAppropriateGift($employee->interests);
                        //If giftID is found!
                        if (!is_null($giftID)) {
                            //Insert record in gift to user and handle integrity constraint
                            try {
                                $employeeModel->assignGiftToUser($employee, $giftID);
                            } catch (QueryException $exception) {
                                $this->getGift($request); //Call the method again to choose other gift
                            }
                            $responseCode = Response::HTTP_OK;
                            $response['message'] = 'Gift assigned successfully!';
                            $giftData = $giftModel->getGiftByID($giftID);
                            $response['data'] = ['gift' => !is_null($giftData) ? $giftData->name : ""];
                        } else {
                            $responseCode = Response::HTTP_BAD_REQUEST;
                            $response['message'] = 'No Gift to assign!';
                        }
                    } else {
                        $responseCode = Response::HTTP_BAD_REQUEST;
                        $response['message'] = 'Gift already assigned';
                        $assignedGift = $employee->gift->first();
                        $response['data'] = ['gift' => !is_null($assignedGift) ? $assignedGift->name : ""];
                    }
                } else { //Employee not found in system
                    $responseCode = Response::HTTP_NOT_FOUND;
                    $response['message'] = 'Employee not found!!';
                }
            } else {
                $responseCode = Response::HTTP_NOT_FOUND;
                $response['message'] = 'Employee not found!!';
            }
        } catch(Exception $exception) {
            $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['error' => 'Something went wrong'];
            parent::log($exception, EmployeeController::class);
        }
        // send response
        return response()->json($response, $responseCode);
    }
}
