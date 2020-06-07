<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Gift extends Model
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
     * categories relationship
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'gift_categories', 'gift_id', 'category_id');
    }
    /***
     * insert gift
     *
     * @param $name
     * @param $giftCategories
     * @param $checkUnique
     *
     * @return int
     */
    public function insertGift($name, $giftCategories, $checkUnique = true) {
        $gift = new $this();
        //If check for uniqueness
        if($checkUnique) {
            $gift = $gift->where('name', '=', $name)->first();
            if (is_null($gift)) {
                $gift = new $this();
            }
        }
        $gift->name = $name;
        $gift->save();
        $gift->categories()->sync($giftCategories);
        return $gift->id;
    }

    /***
     * Get gift by id
     *
     * @param $giftID
     * @return mixed
     */
    public function getGiftByID($giftID)
    {
        $gift = new $this();
        return $gift->where('id', '=', $giftID)->first();
    }

    /***
     * Get appropriate gift
     *
     * @param $interests
     * @return integer|null
     */
    public function getAppropriateGift($interests)
    {
        $giftIDToAssign = null;
        if(!empty($interests)) {
            //Employee interests/categories ID
            $categoriesID = [];
            foreach($interests as $interest) {
                $categoriesID[] = $interest->id;
            }
            $categoriesID = implode(',', $categoriesID);
            $giftToAssign = DB::select("SELECT Group_concat(gc.category_id) AS categories, 
                                                       gc.gift_id 
                                                FROM   gifts AS g 
                                                       JOIN gift_categories gc 
                                                         ON gc.gift_id = g.id 
                                                WHERE  gc.category_id IN ( $categoriesID ) 
                                                       AND g.id NOT IN (SELECT gift_id 
                                                                        FROM   employee_gifts) 
                                                GROUP  BY gc.gift_id 
                                                ORDER  BY Length(Group_concat(gc.category_id)) DESC 
                                                LIMIT  1; ");

            if(!empty($giftToAssign)) {
                $giftIDToAssign = $giftToAssign[0]->gift_id;
            }
        }
        return $giftIDToAssign;
    }


}
