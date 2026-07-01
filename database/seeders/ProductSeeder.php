<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Product;
use App\Models\Region;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    // Pexels rasmlar — hayvon turiga qarab
    private array $images = [
        'sigir' => [
            'https://images.pexels.com/photos/735968/pexels-photo-735968.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/422218/pexels-photo-422218.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1108099/pexels-photo-1108099.jpeg?auto=compress&cs=tinysrgb&w=800',
        ],
        'qoy' => [
            'https://images.pexels.com/photos/288621/pexels-photo-288621.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/207082/pexels-photo-207082.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1300927/pexels-photo-1300927.jpeg?auto=compress&cs=tinysrgb&w=800',
        ],
        'ot' => [
            'https://images.pexels.com/photos/52500/horse-herd-fog-nature-52500.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/414083/pexels-photo-414083.jpeg?auto=compress&cs=tinysrgb&w=800',
        ],
        'echki' => [
            'https://images.pexels.com/photos/1458916/pexels-photo-1458916.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/2315712/pexels-photo-2315712.jpeg?auto=compress&cs=tinysrgb&w=800',
        ],
        'tuya' => [
            'https://images.pexels.com/photos/87406/pexels-photo-87406.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1034823/pexels-photo-1034823.jpeg?auto=compress&cs=tinysrgb&w=800',
        ],
    ];

    // Shahar koordinatalari (taxminiy markaz + kichik offset)
    private array $cityCoords = [
        'Chirchiq'   => [41.469,  69.580],
        'Samarqand'  => [39.654,  66.960],
        'Andijon'    => [40.782,  72.344],
        "Farg'ona"   => [40.386,  71.786],
        'Namangan'   => [41.001,  71.672],
        'Buxoro'     => [39.768,  64.423],
        'Qarshi'     => [38.860,  65.790],
        'Termiz'     => [37.224,  67.278],
        'Nukus'      => [42.460,  59.613],
        'Yunusobod'  => [41.336,  69.285],
        'Navoiy'     => [40.084,  65.379],
        'Urganch'    => [41.549,  60.634],
        'Jizzax'     => [40.115,  67.842],
        'Zarafshon'  => [41.564,  64.201],
        "Xo'jayli"   => [41.975,  60.364],
        'Toshkent'   => [41.299,  69.240],
    ];

    // Har bir mahsulot ma'lumotlari
    private array $products = [
        // --- SIGIRLAR ---
        [
            'name'     => 'Sersut Golshteyn sigir',
            'desc'     => "Kuniga 28 litr sut beradi. 4 yoshli, sog'lom. Barcha emlashlar qilingan. Nasl hujjatlari mavjud.",
            'price'    => 14_500_000,
            'color' => 'Qora', 'category' => 'Sigir',
            'age'      => 4, 'weight' => 480, 'img' => 'sigir', 'imgIdx' => 0,
            'region'   => 'Toshkent viloyati', 'city' => 'Chirchiq shahri',
            'gender' => 'urgochi', 'phone' => '+998901110001',
        ],
        [
            'name'     => "Bo'rdoqi buqa — Simmental",
            'desc'     => "Og'irlik 640 kg. 5 yoshli naslli buqa. Yaxshi tug'imlilik ko'rsatkichlariga ega. Fermerlik uchun ideal.",
            'price'    => 24_000_000,
            'color' => "Qo'ng'ir", 'category' => 'Buqa',
            'age'      => 5, 'weight' => 640, 'img' => 'sigir', 'imgIdx' => 1,
            'region'   => 'Samarqand viloyati', 'city' => 'Samarqand shahri',
            'gender' => 'erkak', 'phone' => '+998901110002',
        ],
        [
            'name'     => "Jersey sigir — sut yo'nalishi",
            'desc'     => "Jersey zotli sigir, kuniga 22 litr yuqori yog'li sut. 3 yoshli, sokin xarakter. Bolali xonadonga mos.",
            'price'    => 13_000_000,
            'color' => "Qo'ng'ir", 'category' => 'Sigir',
            'age'      => 3, 'weight' => 390, 'img' => 'sigir', 'imgIdx' => 2,
            'region'   => 'Andijon viloyati', 'city' => 'Andijon shahri',
            'gender' => 'urgochi', 'phone' => '+998901110003',
        ],
        [
            'name'     => 'Naslli buzoq — 8 oylik',
            'desc'     => "8 oylik Golshteyn naslli buzoq. Ota-onasi ma'lum, tez semiradi. Xo'jalikka qo'shish uchun tayyor.",
            'price'    => 4_200_000,
            'color' => 'Ala', 'category' => 'Buzoq',
            'age'      => 1, 'weight' => 115, 'img' => 'sigir', 'imgIdx' => 0,
            'region'   => "Farg'ona viloyati", 'city' => "Farg'ona shahri",
            'gender' => 'urgochi', 'phone' => '+998901110004',
        ],
        [
            'name'     => 'Qora-ola sigir juftligi',
            'desc'     => "Ikki sigir birga sotiladi. Har biri kuniga 18-20 litr sut beradi. 4 va 5 yoshli.",
            'price'    => 27_000_000,
            'color' => 'Ala', 'category' => 'Sigir',
            'age'      => 4, 'weight' => 450, 'img' => 'sigir', 'imgIdx' => 1,
            'region'   => 'Namangan viloyati', 'city' => 'Namangan shahri',
            'gender' => 'urgochi', 'phone' => '+998901110005',
        ],

        // --- QO'YLAR ---
        [
            'name'     => "Romanov qo'yi — nasl olish uchun",
            'desc'     => "Romanov zotli qo'y — ko'p qo'zilovchi. 2 yoshli, sog'lom. Jun sifati a'lo.",
            'price'    => 3_200_000,
            'color' => 'Oq', 'category' => "Qo'y",
            'age'      => 2, 'weight' => 65, 'img' => 'qoy', 'imgIdx' => 0,
            'region'   => 'Buxoro viloyati', 'city' => 'Buxoro shahri',
            'gender' => 'urgochi', 'phone' => '+998901110006',
        ],
        [
            'name'     => "Gissar qo'chqori — go'shtli",
            'desc'     => "Gissar zoti — go'shti mazali, tez semiradi. 3 yoshli, 85 kg.",
            'price'    => 4_800_000,
            'color' => "Qo'ng'ir", 'category' => "Qo'y",
            'age'      => 3, 'weight' => 85, 'img' => 'qoy', 'imgIdx' => 1,
            'region'   => 'Qashqadaryo viloyati', 'city' => 'Qarshi shahri',
            'gender' => 'erkak', 'phone' => '+998901110007',
        ],
        [
            'name'     => "5 ta qo'zi — birga sotiladi",
            'desc'     => "4-6 oylik 5 ta sog'lom qo'zi. Barcha emlashlar qilingan.",
            'price'    => 9_500_000,
            'color' => 'Oq', 'category' => "Qo'y",
            'age'      => 1, 'weight' => 35, 'img' => 'qoy', 'imgIdx' => 2,
            'region'   => 'Surxondaryo viloyati', 'city' => 'Termiz shahri',
            'gender' => 'urgochi', 'phone' => '+998901110008',
        ],
        [
            'name'     => "Edilbay qo'yi — yog'li dumba",
            'desc'     => "Edilbay — yog'li dumba zoti. 4 yoshli, 72 kg.",
            'price'    => 4_100_000,
            'color' => "Qo'ng'ir", 'category' => "Qo'y",
            'age'      => 4, 'weight' => 72, 'img' => 'qoy', 'imgIdx' => 0,
            'region'   => "Qoraqalpog'iston Respublikasi", 'city' => 'Nukus shahri',
            'gender' => 'urgochi', 'phone' => '+998901110009',
        ],

        // --- ECHKILAR ---
        [
            'name'     => 'Zaanen echki — sersut',
            'desc'     => "Kuniga 5 litr sut. 3 yoshli, 2 marta qo'zilab bo'lgan. Suti mazali va yog'li.",
            'price'    => 2_900_000,
            'color' => 'Oq', 'category' => 'Echki',
            'age'      => 3, 'weight' => 58, 'img' => 'echki', 'imgIdx' => 0,
            'region'   => 'Xorazm viloyati', 'city' => 'Urganch shahri',
            'gender' => 'urgochi', 'phone' => '+998901110010',
        ],
        [
            'name'     => "Echki uloqlari — 3 dona",
            'desc'     => "3-4 oylik, sog'lom uloqlar. Go'shtli yo'nalish uchun mos.",
            'price'    => 1_950_000,
            'color' => 'Ala', 'category' => 'Echki',
            'age'      => 1, 'weight' => 18, 'img' => 'echki', 'imgIdx' => 1,
            'region'   => 'Jizzax viloyati', 'city' => 'Jizzax shahri',
            'gender' => 'erkak', 'phone' => '+998901110011',
        ],

        // --- OTLAR ---
        [
            'name'     => 'Argamak ot — 4 yoshli',
            'desc'     => "Sof qon naslli ot. Tez yurar, ko'rkam. Egar-jabduqlari bilan birga.",
            'price'    => 48_000_000,
            'color' => "Qo'ng'ir", 'category' => 'Ot',
            'age'      => 4, 'weight' => 460, 'img' => 'ot', 'imgIdx' => 0,
            'region'   => 'Toshkent shahri', 'city' => 'Yunusobod tumani',
            'gender' => 'erkak', 'phone' => '+998901110012',
        ],
        [
            'name'     => 'Ishchi ot — kuchli va sokin',
            'desc'     => "Og'ir yuk tashishga o'rgatilgan. 6 yoshli, 530 kg. Sokin xarakter.",
            'price'    => 29_000_000,
            'color' => 'Qora', 'category' => 'Ot',
            'age'      => 6, 'weight' => 530, 'img' => 'ot', 'imgIdx' => 1,
            'region'   => 'Navoiy viloyati', 'city' => 'Navoiy shahri',
            'gender' => 'erkak', 'phone' => '+998901110013',
        ],

        // --- TUYALAR ---
        [
            'name'     => "Baqtrian tuya — ikki o'rkachli",
            'desc'     => "7 yoshli, sog'lom Baqtrian tuya. Xo'jalikda ishlash uchun.",
            'price'    => 88_000_000,
            'color' => "Qo'ng'ir", 'category' => 'Tuya',
            'age'      => 7, 'weight' => 760, 'img' => 'tuya', 'imgIdx' => 0,
            'region'   => "Qoraqalpog'iston Respublikasi", 'city' => "Xo'jayli tumani",
            'gender' => 'erkak', 'phone' => '+998901110014',
        ],
        [
            'name'     => "Dromader tuya — bir o'rkachli",
            'desc'     => "5 yoshli, 610 kg. Cho'l sharoitida boqilgan. Sog'lom va kuchli.",
            'price'    => 74_000_000,
            'color' => 'Sariq', 'category' => 'Tuya',
            'age'      => 5, 'weight' => 610, 'img' => 'tuya', 'imgIdx' => 1,
            'region'   => 'Navoiy viloyati', 'city' => 'Zarafshon shahri',
            'gender' => 'urgochi', 'phone' => '+998901110015',
        ],
    ];

    public function run(): void
    {
        // Eski rasmlarni tozalash
        Storage::disk('public')->deleteDirectory('products');
        Storage::disk('public')->makeDirectory('products');

        // Rasmlarni yuklab olish (tur bo'yicha bitta)
        $this->command->info('  Rasmlar yuklanmoqda...');
        $downloaded = $this->downloadImages();

        $users    = User::all();
        $statusId = Status::where('name', 'Faol')->value('id') ?? Status::first()->id;

        foreach ($this->products as $data) {
            $category = Category::where('name', $data['category'])->first()
                        ?? Category::whereNotNull('parent_id')->first();
            $color    = Color::where('name', $data['color'])->first() ?? Color::first();
            $region   = Region::where('name', $data['region'])->first() ?? Region::inRandomOrder()->first();
            $city     = City::where('region_id', $region->id)->where('name', $data['city'])->first()
                        ?? City::where('region_id', $region->id)->inRandomOrder()->first()
                        ?? City::inRandomOrder()->first();

            $imgKey  = $data['img'] . '-' . $data['imgIdx'];
            $imgPath = $downloaded[$imgKey] ?? null;

            // Koordinatlar: shahar markazi + kichik random offset
            $coords     = $this->cityCoords[$data['city']] ?? null;
            $latOffset  = (mt_rand(-30, 30) / 1000);
            $lngOffset  = (mt_rand(-30, 30) / 1000);

            Product::create([
                'name'          => $data['name'],
                'description'   => $data['desc'],
                'price'         => $data['price'],
                'image'         => $imgPath,
                'category_id'   => $category->id,
                'user_id'       => $users->random()->id,
                'color_id'      => $color->id,
                'age'           => $data['age'],
                'weight'        => $data['weight'],
                'region_id'     => $region->id,
                'city_id'       => $city->id,
                'status_id'     => $statusId,
                'gender'        => $data['gender'] ?? null,
                'contact_phone' => $data['phone']  ?? null,
                'latitude'      => $coords ? round($coords[0] + $latOffset, 7) : null,
                'longitude'     => $coords ? round($coords[1] + $lngOffset, 7) : null,
            ]);
        }

        $this->command->info('  ' . count($this->products) . ' ta mahsulot yaratildi.');
    }

    private function downloadImages(): array
    {
        $downloaded = [];

        foreach ($this->images as $type => $urls) {
            foreach ($urls as $idx => $url) {
                $key      = "{$type}-{$idx}";
                $filename = "products/{$type}-{$idx}.jpg";

                // Allaqachon mavjud bo'lsa, qayta yuklamaslik
                if (Storage::disk('public')->exists($filename)) {
                    $downloaded[$key] = $filename;
                    continue;
                }

                try {
                    $response = Http::timeout(15)->get($url);
                    if ($response->successful()) {
                        Storage::disk('public')->put($filename, $response->body());
                        $downloaded[$key] = $filename;
                        $this->command->line("    ✓ {$filename}");
                    }
                } catch (\Exception $e) {
                    $this->command->warn("    ✗ {$filename} yuklanmadi: " . $e->getMessage());
                }
            }
        }

        return $downloaded;
    }
}
