<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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


}
