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

    // Qoraqalpog'iston tumanlarining taxminiy koordinatalari
    private array $districts = [
        'Nukus shahri'        => [42.460, 59.613],
        'Amudaryo tumani'     => [41.433, 60.383],
        'Beruniy tumani'      => [41.697, 60.749],
        'Chimboy tumani'      => [42.942, 59.786],
        "Ellikqal'a tumani"   => [41.554, 61.864],
        'Kegeyli tumani'      => [42.784, 59.608],
        "Mo'ynoq tumani"      => [43.768, 59.017],
        'Nukus tumani'        => [42.560, 59.900],
        "Qonliko'l tumani"    => [42.858, 60.297],
        "Qorao'zak tumani"    => [43.046, 60.018],
        'Shumanay tumani'     => [42.200, 59.350],
        "Taxtako'pir tumani"  => [42.917, 60.017],
        "To'rtko'l tumani"    => [41.484, 61.008],
        "Xo'jayli tumani"     => [41.975, 60.364],
    ];

    // 20 ta shablon — 7 ta kategoriya bo'yicha almashib keladi
    // (0-6: birinchi to'plam, 7-13: ikkinchi to'plam, 14-19: qo'shimcha variantlar)
    private array $templates = [
        // 0: Sigir
        [
            'name'     => 'Golshteyn sut sigiri',
            'desc'     => "Kuniga 25-30 litr sut beradi. Barcha emlashlar qilingan. Nasl hujjatlari mavjud.",
            'category' => 'Sigir', 'color' => 'Qora', 'gender' => 'urgochi',
            'age' => 4, 'weight' => 480, 'price' => 15_000_000,
            'img' => 'sigir', 'imgIdx' => 0,
        ],
        // 1: Buqa
        [
            'name'     => 'Angus naslli buqa',
            'desc'     => "Go'shtli yo'nalish. 4 yoshli, 680 kg. Nasl hujjatlari bor. Fermerlik uchun ideal.",
            'category' => 'Buqa', 'color' => 'Qora', 'gender' => 'erkak',
            'age' => 4, 'weight' => 680, 'price' => 24_000_000,
            'img' => 'sigir', 'imgIdx' => 1,
        ],
        // 2: Buzoq
        [
            'name'     => "Naslli buzoq — 6 oylik",
            'desc'     => "6 oylik Golshteyn naslli buzoq. Tez semiradi, ota-onasi ma'lum. Emlashlar qilingan.",
            'category' => 'Buzoq', 'color' => 'Ala', 'gender' => 'urgochi',
            'age' => 1, 'weight' => 90, 'price' => 3_800_000,
            'img' => 'sigir', 'imgIdx' => 0,
        ],
        // 3: Qo'y
        [
            'name'     => "Karakul qo'yi — nasldor",
            'desc'     => "Qoraqalpog'iston uchun an'anaviy karakul zoti. Yuqori sifatli jun. 3 yoshli, 60 kg.",
            'category' => "Qo'y", 'color' => 'Qora', 'gender' => 'urgochi',
            'age' => 3, 'weight' => 60, 'price' => 3_200_000,
            'img' => 'qoy', 'imgIdx' => 0,
        ],
        // 4: Echki
        [
            'name'     => 'Zaanen echki — sersut',
            'desc'     => "Kuniga 4-5 litr sut. 3 yoshli. Suti yuqori yog'li va mazali. Sokin xarakter.",
            'category' => 'Echki', 'color' => 'Oq', 'gender' => 'urgochi',
            'age' => 3, 'weight' => 55, 'price' => 2_700_000,
            'img' => 'echki', 'imgIdx' => 0,
        ],
        // 5: Ot
        [
            'name'     => "Qarabayir oti — ishchi",
            'desc'     => "O'zbek tug'ma zoti. 5 yoshli, kuchli va chidamli. Xo'jalik ishlari uchun yaxshi.",
            'category' => 'Ot', 'color' => "Qo'ng'ir", 'gender' => 'erkak',
            'age' => 5, 'weight' => 480, 'price' => 38_000_000,
            'img' => 'ot', 'imgIdx' => 0,
        ],
        // 6: Tuya
        [
            'name'     => "Baqtrian tuya — ikki o'rkachli",
            'desc'     => "7 yoshli Baqtrian tuya. Cho'l sharoitiga moslashgan. Sog'lom va kuchli. Xo'jalikda ishlatilgan.",
            'category' => 'Tuya', 'color' => "Qo'ng'ir", 'gender' => 'erkak',
            'age' => 7, 'weight' => 780, 'price' => 90_000_000,
            'img' => 'tuya', 'imgIdx' => 0,
        ],
        // 7: Sigir (Simmental)
        [
            'name'     => 'Simmental sigir — dual-purpose',
            'desc'     => "Ikki yo'nalish: sut ham, go'sht ham. 5 yoshli, 520 kg. Barcha emlashlar qilingan.",
            'category' => 'Sigir', 'color' => "Qo'ng'ir", 'gender' => 'urgochi',
            'age' => 5, 'weight' => 520, 'price' => 17_000_000,
            'img' => 'sigir', 'imgIdx' => 1,
        ],
        // 8: Buqa (Simmental)
        [
            'name'     => 'Simmental buqa — nasldor',
            'desc'     => "Simmental zoti, 5 yoshli, 720 kg. Yaxshi irsiyat ko'rsatkichlari. Nasl hujjatlari mavjud.",
            'category' => 'Buqa', 'color' => "Qo'ng'ir", 'gender' => 'erkak',
            'age' => 5, 'weight' => 720, 'price' => 28_000_000,
            'img' => 'sigir', 'imgIdx' => 2,
        ],
        // 9: Buzoq (erkak)
        [
            'name'     => "Buzoq — 8 oylik boquvga tayyor",
            'desc'     => "8 oylik, semiz buzoq. Boquvga tayyor. Barcha emlashlar qilingan. Tez semiradi.",
            'category' => 'Buzoq', 'color' => 'Qora', 'gender' => 'erkak',
            'age' => 1, 'weight' => 120, 'price' => 4_500_000,
            'img' => 'sigir', 'imgIdx' => 1,
        ],
        // 10: Qo'y (Edilbay)
        [
            'name'     => "Edilbay qo'yi — yog'li dumba",
            'desc'     => "Edilbay zoti — yog'li dumba, go'shti mazali. 4 yoshli, 75 kg. Cho'l sharoitiga moslashgan.",
            'category' => "Qo'y", 'color' => "Qo'ng'ir", 'gender' => 'urgochi',
            'age' => 4, 'weight' => 75, 'price' => 4_200_000,
            'img' => 'qoy', 'imgIdx' => 1,
        ],
        // 11: Echki (Togenberg)
        [
            'name'     => 'Togenberg echki — sersut',
            'desc'     => "Togenberg zoti, 2 yoshli. Kuniga 3-4 litr sut. Sokin xarakter, bog'liq oilalar uchun mos.",
            'category' => 'Echki', 'color' => 'Kulrang', 'gender' => 'urgochi',
            'age' => 2, 'weight' => 45, 'price' => 2_200_000,
            'img' => 'echki', 'imgIdx' => 1,
        ],
        // 12: Ot (Yabou biyasi)
        [
            'name'     => "Yabou biyasi — qimiz uchun",
            'desc'     => "Qoraqalpog'iston mahalliy zoti. 4 yoshli, qimiz ishlab chiqarish uchun mo'ljallangan.",
            'category' => 'Ot', 'color' => 'Sariq', 'gender' => 'urgochi',
            'age' => 4, 'weight' => 420, 'price' => 28_000_000,
            'img' => 'ot', 'imgIdx' => 1,
        ],
        // 13: Tuya (Dromader)
        [
            'name'     => "Dromader tuya — bir o'rkachli",
            'desc'     => "5 yoshli, 620 kg. Cho'l sharoitida boqilgan. Sog'lom va kuchli. Yuki ko'tarishga o'rgatilgan.",
            'category' => 'Tuya', 'color' => 'Sariq', 'gender' => 'urgochi',
            'age' => 5, 'weight' => 620, 'price' => 72_000_000,
            'img' => 'tuya', 'imgIdx' => 1,
        ],
        // 14: Sigir (Jersey)
        [
            'name'     => "Jersey sigir — yuqori yog'li sut",
            'desc'     => "Jersey zotli, kuniga 20 litr yuqori yog'li sut. 3 yoshli, sokin xarakter. Bolali oilaga mos.",
            'category' => 'Sigir', 'color' => "Qo'ng'ir", 'gender' => 'urgochi',
            'age' => 3, 'weight' => 380, 'price' => 12_500_000,
            'img' => 'sigir', 'imgIdx' => 2,
        ],
        // 15: Qo'y (Gissar qo'chqori)
        [
            'name'     => "Gissar qo'chqori — go'shtli",
            'desc'     => "Gissar zoti, 3 yoshli, 90 kg. Go'shti mazali, tez semiradi. Kurban uchun ideal.",
            'category' => "Qo'y", 'color' => 'Oq', 'gender' => 'erkak',
            'age' => 3, 'weight' => 90, 'price' => 5_500_000,
            'img' => 'qoy', 'imgIdx' => 2,
        ],
        // 16: Ot (Argamak)
        [
            'name'     => 'Argamak ot — sport uchun',
            'desc'     => "Sof qon naslli argamak. 4 yoshli, tez yurar, ko'rkam. Egar-jabduqlari bilan birga.",
            'category' => 'Ot', 'color' => 'Qora', 'gender' => 'erkak',
            'age' => 4, 'weight' => 460, 'price' => 52_000_000,
            'img' => 'ot', 'imgIdx' => 0,
        ],
        // 17: Sigir (Mahalliy)
        [
            'name'     => "Mahalliy sigir — issiqqa chidamli",
            'desc'     => "Mahalliy zot, issiqqa chidamli. Oz ovqat bilan yaxshi sut beradi. 4 yoshli, 420 kg.",
            'category' => 'Sigir', 'color' => 'Ala', 'gender' => 'urgochi',
            'age' => 4, 'weight' => 420, 'price' => 11_000_000,
            'img' => 'sigir', 'imgIdx' => 0,
        ],
        // 18: Qo'y (Romanov)
        [
            'name'     => "Romanov qo'yi — ko'p qo'zilovchi",
            'desc'     => "Romanov zoti, bir martta 2-3 qo'zi. 2 yoshli, sog'lom. Jun sifati a'lo.",
            'category' => "Qo'y", 'color' => 'Oq', 'gender' => 'urgochi',
            'age' => 2, 'weight' => 55, 'price' => 2_800_000,
            'img' => 'qoy', 'imgIdx' => 0,
        ],
        // 19: Echki (Taka)
        [
            'name'     => 'Echki tekasi — nasldor',
            'desc'     => "3 yoshli naslli taka. Yaxshi irsiyat. Nasl hujjatlari mavjud. Sog'lom va kuchli.",
            'category' => 'Echki', 'color' => 'Ala', 'gender' => 'erkak',
            'age' => 3, 'weight' => 65, 'price' => 2_500_000,
            'img' => 'echki', 'imgIdx' => 0,
        ],
    ];

    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('products');
        Storage::disk('public')->makeDirectory('products');

        $this->command->info('  Rasmlar yuklanmoqda...');
        $downloaded = $this->downloadImages();

        $users    = User::all();
        $statusId = Status::where('name', 'Faol')->value('id') ?? Status::first()->id;
        $region   = Region::where('name', "Qoraqalpog'iston Respublikasi")->first();

        if (! $region) {
            $this->command->error("Qoraqalpog'iston Respublikasi topilmadi!");
            return;
        }

        $districtNames  = array_keys($this->districts);
        $districtCount  = count($districtNames);   // 14
        $totalProducts  = 100;
        $basePer        = (int) floor($totalProducts / $districtCount); // 7
        $extras         = $totalProducts - ($basePer * $districtCount); // 100 - 98 = 2
        $templateCount  = count($this->templates);  // 20
        $phoneCounter   = 200;
        $templateCursor = 0;

        foreach ($districtNames as $dIdx => $districtName) {
            $count  = $basePer + ($dIdx < $extras ? 1 : 0);
            $coords = $this->districts[$districtName];

            $city = City::where('region_id', $region->id)
                        ->where('name', $districtName)
                        ->first()
                    ?? City::where('region_id', $region->id)->inRandomOrder()->first();

            for ($i = 0; $i < $count; $i++) {
                $tmpl = $this->templates[$templateCursor % $templateCount];
                $templateCursor++;

                $category = Category::where('name', $tmpl['category'])->first()
                            ?? Category::whereNotNull('parent_id')->first();
                $color    = Color::where('name', $tmpl['color'])->first() ?? Color::first();

                // Narx, og'irlik va yoshga kichik variatsiya
                $priceJitter  = (int) (mt_rand(-8, 8) / 100 * $tmpl['price']);
                $weightJitter = mt_rand(-15, 15);
                $ageJitter    = mt_rand(0, 1);

                $latOffset = mt_rand(-60, 60) / 1000;
                $lngOffset = mt_rand(-60, 60) / 1000;

                $phoneCounter++;
                $imgKey  = $tmpl['img'] . '-' . $tmpl['imgIdx'];
                $imgPath = $downloaded[$imgKey] ?? null;

                Product::create([
                    'name'          => $tmpl['name'],
                    'description'   => $tmpl['desc'],
                    'price'         => max(1_000_000, $tmpl['price'] + $priceJitter),
                    'image'         => $imgPath,
                    'category_id'   => $category->id,
                    'user_id'       => $users->random()->id,
                    'color_id'      => $color->id,
                    'age'           => max(1, $tmpl['age'] + $ageJitter),
                    'weight'        => max(10, $tmpl['weight'] + $weightJitter),
                    'region_id'     => $region->id,
                    'city_id'       => $city->id,
                    'status_id'     => $statusId,
                    'gender'        => $tmpl['gender'],
                    'contact_phone' => '+9989011' . str_pad($phoneCounter, 5, '0', STR_PAD_LEFT),
                    'latitude'      => round($coords[0] + $latOffset, 7),
                    'longitude'     => round($coords[1] + $lngOffset, 7),
                ]);
            }

            $this->command->line("    ✓ {$districtName}: {$count} ta mahsulot");
        }

        $this->command->info("  Jami {$totalProducts} ta mahsulot yaratildi.");
    }

    private function downloadImages(): array
    {
        $downloaded = [];

        foreach ($this->images as $type => $urls) {
            foreach ($urls as $idx => $url) {
                $key      = "{$type}-{$idx}";
                $filename = "products/{$type}-{$idx}.jpg";

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
