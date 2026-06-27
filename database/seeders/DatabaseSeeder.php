<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Product;
use App\Models\Region;
use App\Models\Status;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Lookup jadvallar...');
        $this->seedTypes();
        $this->seedColors();
        $this->seedStatuses();
        $this->seedRegionsAndCities();
        $this->seedCategories();

        $this->command->info('Foydalanuvchilar...');
        $this->seedUsers();

        $this->command->info('Mahsulotlar (rasmli)...');
        $this->call(ProductSeeder::class);

        $this->command->info('Qo\'shimcha fake mahsulotlar (factory)...');
        Product::factory(10)->create();

        $this->command->info('Tayyor!');
    }

    private function seedTypes(): void
    {
        foreach (['Sigir', "Qo'y", 'Echki', 'Ot', 'Tuya', 'Buqa', 'Qoramol'] as $name) {
            Type::firstOrCreate(['name' => $name]);
        }
    }

    private function seedColors(): void
    {
        foreach (['Qora', 'Oq', "Qo'ng'ir", 'Sariq', 'Qizil', 'Kulrang', 'Ala'] as $name) {
            Color::firstOrCreate(['name' => $name]);
        }
    }

    private function seedStatuses(): void
    {
        foreach (['Faol', 'Sotildi', "Ko'rib chiqilmoqda", 'Arxivlangan'] as $name) {
            Status::firstOrCreate(['name' => $name]);
        }
    }

    private function seedRegionsAndCities(): void
    {
        $data = [
            'Toshkent shahri'      => ['Yunusobod', 'Chilonzor', 'Shayxontohur', "Mirzo Ulug'bek"],
            'Toshkent viloyati'    => ['Angren', 'Chirchiq', 'Bekabad', 'Olmaliq'],
            'Samarqand viloyati'   => ['Samarqand', "Kattaqo'rg'on", 'Urgut'],
            "Farg'ona viloyati"    => ["Farg'ona", "Marg'ilon", "Qo'qon"],
            'Buxoro viloyati'      => ['Buxoro', "G'ijduvon", 'Kogon'],
            'Andijon viloyati'     => ['Andijon', 'Asaka', 'Xonobod'],
            'Namangan viloyati'    => ['Namangan', 'Chortoq', 'Kosonsoy'],
            'Qashqadaryo viloyati' => ['Qarshi', 'Shahrisabz'],
            'Surxondaryo viloyati' => ['Termiz', 'Denov'],
            'Xorazm viloyati'      => ['Urganch', 'Xiva'],
            'Navoiy viloyati'      => ['Navoiy', 'Zarafshon'],
            'Jizzax viloyati'      => ['Jizzax', "G'allaorol"],
            'Sirdaryo viloyati'    => ['Guliston', 'Sirdaryo'],
            "Qoraqalpog'iston"     => ['Nukus', 'Beruniy', "Xo'jayli"],
        ];

        foreach ($data as $regionName => $cities) {
            $region = Region::firstOrCreate(['name' => $regionName]);
            foreach ($cities as $cityName) {
                City::firstOrCreate(['region_id' => $region->id, 'name' => $cityName]);
            }
        }
    }

    private function seedCategories(): void
    {
        $tree = [
            ['name' => 'Qoramol',       'slug' => 'qoramol',   'children' => [
                ['name' => 'Sigir',  'slug' => 'sigir'],
                ['name' => 'Buqa',   'slug' => 'buqa'],
                ['name' => 'Buzoq',  'slug' => 'buzoq'],
            ]],
            ['name' => "Qo'y va echki", 'slug' => 'qoy-echki', 'children' => [
                ['name' => "Qo'y",   'slug' => 'qoy'],
                ['name' => 'Echki',  'slug' => 'echki'],
            ]],
            ['name' => 'Ot va tuya',    'slug' => 'ot-tuya',   'children' => [
                ['name' => 'Ot',     'slug' => 'ot'],
                ['name' => 'Tuya',   'slug' => 'tuya'],
            ]],
        ];

        foreach ($tree as $cat) {
            $children = $cat['children'];
            $parent   = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'parent_id' => null, 'icon' => null]
            );
            foreach ($children as $child) {
                Category::firstOrCreate(
                    ['slug' => $child['slug']],
                    ['name' => $child['name'], 'parent_id' => $parent->id, 'icon' => null]
                );
            }
        }
    }

    private function seedUsers(): void
    {
        $users = [
            ['first_name' => 'Admin',    'last_name' => 'User',      'phone' => '+998901234567'],
            ['first_name' => 'Jasur',    'last_name' => 'Toshmatov', 'phone' => '+998901111111'],
            ['first_name' => 'Nodira',   'last_name' => 'Karimova',  'phone' => '+998902222222'],
            ['first_name' => 'Bobur',    'last_name' => "Yo'ldoshev", 'phone' => '+998903333333'],
            ['first_name' => 'Malika',   'last_name' => 'Rahimova',  'phone' => '+998904444444'],
            ['first_name' => 'Sanjar',   'last_name' => 'Usmonov',   'phone' => '+998905555555'],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['phone' => $data['phone']],
                array_merge($data, ['password' => Hash::make('password')])
            );
        }
    }
}
