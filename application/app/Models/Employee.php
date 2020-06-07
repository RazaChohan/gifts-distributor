<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    /***
     * No timestamps
     *
     * @var bool
     */
    public $timestamps = false;
    /***
     * Interests relationship
     */
    public function interests()
    {
        return $this->belongsToMany(Category::class, 'employee_interests', 'employee_id', 'category_id');
    }

    /***
     * Gift relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function gift()
    {
        return $this->belongsToMany(Gift::class, 'employee_gifts', 'employee_id', 'gift_id');
    }
    /***
     * insert employee
     *
     * @param $name
     * @param $employeeInterests
     * @param $checkUnique
     *
     * @return int
     */
    public function insertEmployee($name, $employeeInterests, $checkUnique = true) {
        $employee = new $this();
        //If check for uniqueness
        if($checkUnique) {
            $employee = $employee->where('name', '=', $name)->first();
            if (is_null($employee)) {
                $employee = new $this();
            }
        }
        $employee->name = $name;
        $employee->save();
        $employee->interests()->sync($employeeInterests);
        return $employee->id;
    }

    /***
     * Get employee
     *
     * @param $employeeID
     * @return mixed
     */
    public function getEmployeeByID($employeeID)
    {
        $employee = new $this();
        return $employee->where('id', '=', $employeeID)->first();
    }

    /***
     * Assign gift to user
     *
     * @param $employee
     * @param $giftID
     */
    public function assignGiftToUser($employee, $giftID)
    {
        $employee->gift()->attach($giftID);
    }


}
