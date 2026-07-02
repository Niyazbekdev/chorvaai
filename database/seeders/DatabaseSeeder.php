<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Region;
use App\Models\Role;
use App\Models\Status;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Lookup jadvallar...');
        $this->seedRoles();
        $this->seedTypes();
        $this->seedColors();
        $this->seedStatuses();
        $this->seedRegionsAndCities();
        $this->seedCategories();

        $this->command->info('Foydalanuvchilar...');
        $this->seedUsers();

        $this->command->info('Mahsulotlar (rasmli)...');
        $this->call(ProductSeeder::class);

        $this->command->info('Tayyor!');
    }

    private function seedRoles(): void
    {
        Role::firstOrCreate(['slug' => 'admin'],    ['name' => 'Admin']);
        Role::firstOrCreate(['slug' => 'customer'], ['name' => 'Customer']);
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
            'Toshkent shahri' => [
                'Bektemir tumani', 'Chilonzor tumani', 'Mirzo Ulug\'bek tumani',
                'Mirobod tumani', 'Olmazor tumani', 'Sergeli tumani',
                'Shayxontohur tumani', 'Uchtepa tumani', 'Yashnobod tumani',
                'Yunusobod tumani', 'Yakkasaroy tumani',
            ],
            'Toshkent viloyati' => [
                'Angren shahri', 'Bekabad shahri', 'Chirchiq shahri',
                'Olmaliq shahri', 'Yangiyo\'l shahri',
                'Bo\'ka tumani', 'Bo\'stonliq tumani', 'Chinoz tumani',
                'Iskandar tumani', 'Kibray tumani', 'Oqqo\'rg\'on tumani',
                'O\'rtachirchiq tumani', 'Parkent tumani', 'Piskent tumani',
                'Quyichirchiq tumani', 'Toshkent tumani', 'Yuqorichirchiq tumani',
                'Zangiota tumani',
            ],
            'Andijon viloyati' => [
                'Andijon shahri', 'Asaka shahri', 'Xonobod shahri',
                'Andijon tumani', 'Asaka tumani', 'Baliqchi tumani',
                'Bo\'ston tumani', 'Buloqboshi tumani', 'Izboskan tumani',
                'Jalaquduq tumani', 'Marhamat tumani', 'Oltinko\'l tumani',
                'Paxtaobod tumani', 'Qo\'rg\'ontepa tumani', 'Shahrixon tumani',
                'Ulugnor tumani', 'Xo\'jaobod tumani',
            ],
            'Farg\'ona viloyati' => [
                'Farg\'ona shahri', 'Marg\'ilon shahri', 'Qo\'qon shahri',
                'Bag\'dod tumani', 'Beshariq tumani', 'Buvayda tumani',
                'Dang\'ara tumani', 'Furqat tumani', 'Qo\'shtepa tumani',
                'Oltiariq tumani', 'O\'zbekiston tumani', 'Rishton tumani',
                'So\'x tumani', 'Toshloq tumani', 'Uchko\'prik tumani',
                'Yozyovon tumani',
            ],
            'Namangan viloyati' => [
                'Namangan shahri', 'Chortoq shahri', 'Chust shahri',
                'Kosonsoy tumani', 'Mingbuloq tumani', 'Namangan tumani',
                'Norin tumani', 'Pop tumani', 'To\'raqo\'rg\'on tumani',
                'Uchqo\'rg\'on tumani', 'Uychi tumani', 'Yangiqo\'rg\'on tumani',
            ],
            'Samarqand viloyati' => [
                'Samarqand shahri', 'Kattaqo\'rg\'on shahri',
                'Bulung\'ur tumani', 'Ishtixon tumani', 'Jomboy tumani',
                'Kattaqo\'rg\'on tumani', 'Narpay tumani', 'Nurobod tumani',
                'Oqdaryo tumani', 'Paxtachi tumani', 'Payariq tumani',
                'Pastdarg\'om tumani', 'Qo\'shrabot tumani', 'Toyloq tumani',
                'Urgut tumani',
            ],
            'Buxoro viloyati' => [
                'Buxoro shahri', 'Kogon shahri',
                'G\'ijduvon tumani', 'Jondor tumani', 'Kogon tumani',
                'Olot tumani', 'Peshku tumani', 'Qorakol tumani',
                'Qorovulbozor tumani', 'Romitan tumani', 'Shofirkon tumani',
                'Vobkent tumani',
            ],
            'Navoiy viloyati' => [
                'Navoiy shahri', 'Zarafshon shahri',
                'Karmana tumani', 'Konimex tumani', 'Navbahor tumani',
                'Nurota tumani', 'Qiziltepa tumani', 'Tomdi tumani',
                'Uchquduq tumani', 'Xatirchi tumani',
            ],
            'Qashqadaryo viloyati' => [
                'Qarshi shahri', 'Shahrisabz shahri',
                'Chiroqchi tumani', 'Dehqonobod tumani', 'G\'uzor tumani',
                'Kasbi tumani', 'Kitob tumani', 'Koson tumani',
                'Mirishkor tumani', 'Muborak tumani', 'Nishon tumani',
                'Qamashi tumani', 'Shahrisabz tumani', 'Yakkabog\' tumani',
            ],
            'Surxondaryo viloyati' => [
                'Termiz shahri',
                'Angor tumani', 'Bandixon tumani', 'Boysun tumani',
                'Denov tumani', 'Jarqo\'rg\'on tumani', 'Muzrabot tumani',
                'Oltinsoy tumani', 'Qiziriq tumani', 'Qumqo\'rg\'on tumani',
                'Sariosiyo tumani', 'Sherobod tumani', 'Sho\'rchi tumani',
                'Uzun tumani',
            ],
            'Xorazm viloyati' => [
                'Urganch shahri', 'Xiva shahri',
                'Bog\'ot tumani', 'Gurlan tumani', 'Xazorasp tumani',
                'Xiva tumani', 'Xonqa tumani', 'Qo\'shko\'pir tumani',
                'Shovot tumani', 'Tuproqqal\'a tumani', 'Yangiariq tumani',
                'Yangibozor tumani',
            ],
            'Jizzax viloyati' => [
                'Jizzax shahri',
                'Arnasoy tumani', 'Baxmal tumani', 'Do\'stlik tumani',
                'Forish tumani', 'G\'allaorol tumani', 'Mirzacho\'l tumani',
                'Paxtakor tumani', 'Yangiobod tumani', 'Zarbdor tumani',
                'Zafarobod tumani', 'Zomin tumani',
            ],
            'Sirdaryo viloyati' => [
                'Guliston shahri', 'Sirdaryo shahri',
                'Boyovut tumani', 'Mirzaobod tumani', 'Oqoltin tumani',
                'Sardoba tumani', 'Sayxunobod tumani', 'Sirdaryo tumani',
                'Xavast tumani',
            ],
            'Qoraqalpog\'iston Respublikasi' => [
                'Nukus shahri',
                'Amudaryo tumani', 'Beruniy tumani', 'Chimboy tumani',
                'Ellikqal\'a tumani', 'Kegeyli tumani', 'Mo\'ynoq tumani',
                'Nukus tumani', 'Qonliko\'l tumani', 'Qorao\'zak tumani',
                'Shumanay tumani', 'Taxtako\'pir tumani', 'To\'rtko\'l tumani',
                'Xo\'jayli tumani',
            ],
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
        $adminRole    = Role::where('slug', 'admin')->first();
        $customerRole = Role::where('slug', 'customer')->first();

        $users = [
            ['first_name' => 'Admin',  'last_name' => 'User',       'phone' => '+998901234567', 'role_id' => $adminRole?->id],
            ['first_name' => 'Jasur',  'last_name' => 'Toshmatov',  'phone' => '+998901111111', 'role_id' => $customerRole?->id],
            ['first_name' => 'Nodira', 'last_name' => 'Karimova',   'phone' => '+998902222222', 'role_id' => $customerRole?->id],
            ['first_name' => 'Bobur',  'last_name' => "Yo'ldoshev", 'phone' => '+998903333333', 'role_id' => $customerRole?->id],
            ['first_name' => 'Malika', 'last_name' => 'Rahimova',   'phone' => '+998904444444', 'role_id' => $customerRole?->id],
            ['first_name' => 'Sanjar', 'last_name' => 'Usmonov',    'phone' => '+998905555555', 'role_id' => $customerRole?->id],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['phone' => $data['phone']],
                array_merge($data, [
                    'password'          => 'password',
                    'phone_verified_at' => now(),
                ])
            );
        }
    }
}
