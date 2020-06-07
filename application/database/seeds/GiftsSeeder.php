<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Gift;
use App\Models\Category;

class GiftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $storagePath  = Storage::disk('local')->path('gifts.json');
        //Check if file exists
        if(file_exists($storagePath)) {
            $gifts = json_decode(file_get_contents($storagePath), true);
            //If $gifts are not empty
            $giftModel = new Gift();
            $categoryModel = new Category();
            if(!empty($gifts)) {
                foreach($gifts as $gift) {
                    $categories = $gift['categories'];
                    $giftCategories = [];
                    //Insert categories
                    foreach($categories as $category) {
                        $giftCategories[] = $categoryModel->insertCategory($category);
                    }
                    $giftModel->insertGift($gift['name'], $giftCategories);
                }
            }
        } else {
            exit('Employees File not found!!');
        }
    }
}