<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommoditiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $commodities = [
            ['name' => 'Cassava', 'icon' => 'icons/cassava.png'],
            ['name' => 'Mango', 'icon' => 'icons/mango.png'],
            ['name' => 'Sweet Potato', 'icon' => 'icons/sweet-potato.png'],
            ['name' => 'Tilapia', 'icon' => 'icons/tilapia.png'],
            ['name' => 'Dairy', 'icon' => 'icons/dairy.png'],
            ['name' => 'Goat', 'icon' => 'icons/goat.png'],
            ['name' => 'Organic garlic', 'icon' => 'icons/garlic.png'],
            ['name' => 'Banana', 'icon' => 'icons/banana.png'],
            ['name' => 'Coffee', 'icon' => 'icons/coffee.png'],
            ['name' => 'Citrus (Satsuma, Mandarin)', 'icon' => 'icons/citrus.png'],
            ['name' => 'Cacao', 'icon' => 'icons/cacao.png'],
            ['name' => 'Coconut', 'icon' => 'icons/coconut.png'],
            ['name' => 'Rubber', 'icon' => 'icons/rubber.png'],
            ['name' => 'Onion', 'icon' => 'icons/onion.png'],
            ['name' => 'Tuna', 'icon' => 'icons/tuna.png'],
            ['name' => 'Ampalaya', 'icon' => 'icons/ampalaya.png'],
            ['name' => 'Abaca', 'icon' => 'icons/abaca.png'],
            ['name' => 'Oil Palm', 'icon' => 'icons/oil-palm.png'],
            ['name' => 'Beef Cattle', 'icon' => 'icons/beef-cattle.png'],
            ['name' => 'Chicken', 'icon' => 'icons/chicken.png'],
            ['name' => 'Seaweeds', 'icon' => 'icons/seaweed.png'],
            ['name' => 'Pineapple', 'icon' => 'icons/pineapple.png'],
            ['name' => 'Aromatic/Pigmented Rice', 'icon' => 'icons/rice.png'],
            ['name' => 'Mungbean', 'icon' => 'icons/mungbean.png'],
            ['name' => 'Pili', 'icon' => 'icons/pili.png'],
            ['name' => 'Arrowroot', 'icon' => 'icons/arrowroot.png'],
            ['name' => 'Calamansi', 'icon' => 'icons/calamansi.png'],
            ['name' => 'Cashew', 'icon' => 'icons/cashew.png'],
            ['name' => 'Dairy Milk', 'icon' => 'icons/milk.png'],
        ];

        DB::table('commodities')->insert($commodities);
    }
}
