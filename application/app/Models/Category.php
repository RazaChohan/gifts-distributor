<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
     * insert category
     *
     * @param $name
     * @param $checkUnique
     *
     * @return int
     */
    public function insertCategory($name, $checkUnique = true) {
        $category = new $this();
        //If check for uniqueness
        if($checkUnique) {
            $category = $category->where('name', '=', $name)->first();
            if (is_null($category)) {
                $category = new $this();
            }
        }
        $category->name = $name;
        $category->save();
        return $category->id;
    }
}
