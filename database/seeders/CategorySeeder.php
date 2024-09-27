<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            'name' => 'Café',
            'icon' => 'icono_cafe.svg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        DB::table('categories')->insert([
            'name' => 'Hamburguesas',
            'icon' => 'icono_hamburguesa.svg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        DB::table('categories')->insert([
            'name' => 'Pizzas',
            'icon' => 'icono_pizza.svg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        DB::table('categories')->insert([
            'name' => 'Donas',
            'icon' => 'icono_dona.svg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        DB::table('categories')->insert([
            'name' => 'Pasteles',
            'icon' => 'icono_pastel.svg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        DB::table('categories')->insert([
            'name' => 'Galletas',
            'icon' => 'icono_galletas.svg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        self::saveImage('icono_cafe.svg');
        self::saveImage('icono_hamburguesa.svg');
        self::saveImage('icono_pizza.svg');
        self::saveImage('icono_dona.svg');
        self::saveImage('icono_pastel.svg');
        self::saveImage('icono_galletas.svg');
    }

    public static function saveImage(String $name)
    {
        $file = File::get(public_path("/img/{$name}"));
        if($file)
            Storage::put("categories/{$name}", $file);
    }
}
