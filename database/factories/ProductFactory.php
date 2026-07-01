<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Region;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    private static array $names = [
        'Sersut sigir', "Bo'rdoqi qo'y", 'Naslli buqa', 'Semiz echki',
        'Ishchi ot', 'Kuchli qoramol', "Yosh qo'zi", 'Toza naslli tuya',
        "Golshteyn sigir", 'Argamak ot', "Zaanen echki", "Romanov qo'yi",
        "Gissar qo'chqori", 'Jersey sigir', 'Baqtrian tuya',
    ];

    private static array $descriptions = [
        "Sog'lom va kuchli hayvon. Barcha emlashlar o'z vaqtida qilingan. Sotilish sababi — xo'jalikni qisqartirish.",
        "Nasl hujjatlari mavjud. Ko'rikdan o'tgan. Egar-jabduqlari bilan birga beriladi.",
        "Fermer xo'jaligida boqilgan. Tabiiy em bilan oziqlantirilgan. Veterinar ko'rigidan o'tgan.",
        "Tez semiradi, go'shti mazali. 3 avlod nasl ma'lumotlari mavjud.",
        "Kuniga ko'p sut beradi. Sokin xarakter, bola-chaqali xonadonga mos.",
        "Kuchli immunitet. Sovuq iqlimga moslashgan. Birga boqilgani uchun juft sotuv ham mumkin.",
    ];

    public function definition(): array
    {
        $region = Region::inRandomOrder()->first() ?? Region::factory()->create();
        $city   = City::where('region_id', $region->id)->inRandomOrder()->first()
                  ?? City::inRandomOrder()->first();

        return [
            'name'        => fake()->randomElement(self::$names) . ' — ' . fake()->numberBetween(1, 5) . ' yoshli',
            'description' => fake()->randomElement(self::$descriptions),
            'price'       => fake()->numberBetween(1, 90) * 500_000,
            'image'       => null,
            'category_id' => fn () => Category::whereNotNull('parent_id')->inRandomOrder()->value('id'),
            'user_id'     => fn () => User::inRandomOrder()->value('id'),
            'color_id'    => fn () => Color::inRandomOrder()->value('id'),
            'age'         => fake()->numberBetween(1, 12),
            'weight'      => fake()->numberBetween(30, 700),
            'region_id'   => $region->id,
            'city_id'     => $city?->id ?? City::inRandomOrder()->value('id'),
            'status_id'   => fn () => Status::inRandomOrder()->value('id'),
        ];
    }
}
