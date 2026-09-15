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
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    // 14 ta e'lon: 7 mol + 7 qoy
    private array $templates = [
        // --- MOLLAR (7 ta) ---
        [
            'name'      => 'Golshteyn sut sigiri',
            'desc'      => "Kuniga 25-30 litr sut beradi. Barcha emlashlar qilingan. Nasl hujjatlari mavjud.",
            'category'  => 'Sigir', 'color' => 'Qora', 'gender' => 'urgochi',
            'age' => 4, 'weight' => 480, 'price' => 15_000_000,
            'folder' => 'mollar', 'file' => '2.jpeg',
        ],
        [
            'name'      => 'Angus naslli buqa',
            'desc'      => "Go'shtli yo'nalish. 4 yoshli, 680 kg. Nasl hujjatlari bor. Fermerlik uchun ideal.",
            'category'  => 'Buqa', 'color' => 'Qora', 'gender' => 'erkak',
            'age' => 4, 'weight' => 680, 'price' => 24_000_000,
            'folder' => 'mollar', 'file' => 'Mol_(buqa).jpg',
        ],
        [
            'name'      => 'Simmental sigir — dual-purpose',
            'desc'      => "Ikki yo'nalish: sut ham, go'sht ham. 5 yoshli, 520 kg. Barcha emlashlar qilingan.",
            'category'  => 'Sigir', 'color' => "Qo'ng'ir", 'gender' => 'urgochi',
            'age' => 5, 'weight' => 520, 'price' => 17_000_000,
            'folder' => 'mollar', 'file' => 'images_1.jpeg',
        ],
        [
            'name'      => 'Mahalliy sigir — issiqqa chidamli',
            'desc'      => "Mahalliy zot, issiqqa chidamli. Oz ovqat bilan yaxshi sut beradi. 4 yoshli, 420 kg.",
            'category'  => 'Sigir', 'color' => 'Ala', 'gender' => 'urgochi',
            'age' => 4, 'weight' => 420, 'price' => 11_000_000,
            'folder' => 'mollar', 'file' => 'images_2.jpeg',
        ],
        [
            'name'      => "Jersey sigir — yuqori yog'li sut",
            'desc'      => "Jersey zotli, kuniga 20 litr yuqori yog'li sut. 3 yoshli, sokin xarakter.",
            'category'  => 'Sigir', 'color' => "Qo'ng'ir", 'gender' => 'urgochi',
            'age' => 3, 'weight' => 380, 'price' => 12_500_000,
            'folder' => 'mollar', 'file' => 'images.jpeg',
        ],
        [
            'name'      => 'Simmental buqa — nasldor',
            'desc'      => "Simmental zoti, 5 yoshli, 720 kg. Yaxshi irsiyat ko'rsatkichlari. Nasl hujjatlari mavjud.",
            'category'  => 'Buqa', 'color' => "Qo'ng'ir", 'gender' => 'erkak',
            'age' => 5, 'weight' => 720, 'price' => 28_000_000,
            'folder' => 'mollar', 'file' => 'image11.webp',
        ],
        [
            'name'      => "Naslli buzoq — 6 oylik",
            'desc'      => "6 oylik Golshteyn naslli buzoq. Tez semiradi, ota-onasi ma'lum. Emlashlar qilingan.",
            'category'  => 'Buzoq', 'color' => 'Ala', 'gender' => 'urgochi',
            'age' => 1, 'weight' => 90, 'price' => 3_800_000,
            'folder' => 'mollar', 'file' => 'image.webp',
        ],

        // --- QOYLAR (7 ta) ---
        [
            'name'      => "Karakul qo'yi — nasldor",
            'desc'      => "Qoraqalpog'iston uchun an'anaviy karakul zoti. Yuqori sifatli jun. 3 yoshli, 60 kg.",
            'category'  => "Qo'y", 'color' => 'Qora', 'gender' => 'urgochi',
            'age' => 3, 'weight' => 60, 'price' => 3_200_000,
            'folder' => 'qoylar', 'file' => 'image.jpg',
        ],
        [
            'name'      => "Edilbay qo'yi — yog'li dumba",
            'desc'      => "Edilbay zoti — yog'li dumba, go'shti mazali. 4 yoshli, 75 kg. Cho'l sharoitiga moslashgan.",
            'category'  => "Qo'y", 'color' => "Qo'ng'ir", 'gender' => 'urgochi',
            'age' => 4, 'weight' => 75, 'price' => 4_200_000,
            'folder' => 'qoylar', 'file' => 'images.jpeg',
        ],
        [
            'name'      => "Gissar qo'chqori — go'shtli",
            'desc'      => "Gissar zoti, 3 yoshli, 90 kg. Go'shti mazali, tez semiradi. Kurban uchun ideal.",
            'category'  => "Qo'y", 'color' => 'Oq', 'gender' => 'erkak',
            'age' => 3, 'weight' => 90, 'price' => 5_500_000,
            'folder' => 'qoylar', 'file' => 'imag2e.jpg',
        ],
        [
            'name'      => "Romanov qo'yi — ko'p qo'zilovchi",
            'desc'      => "Romanov zoti, bir martta 2-3 qo'zi. 2 yoshli, sog'lom. Jun sifati a'lo.",
            'category'  => "Qo'y", 'color' => 'Oq', 'gender' => 'urgochi',
            'age' => 2, 'weight' => 55, 'price' => 2_800_000,
            'folder' => 'qoylar', 'file' => 'photo_2024-04-25_18-22-09.jpg',
        ],
        [
            'name'      => "Karakul qo'chqori — nasl uchun",
            'desc'      => "Karakul zoti, 2 yoshli qo'chqor. Nasl hujjatlari mavjud. Sog'lom va kuchli.",
            'category'  => "Qo'y", 'color' => 'Qora', 'gender' => 'erkak',
            'age' => 2, 'weight' => 65, 'price' => 3_500_000,
            'folder' => 'qoylar', 'file' => 'image.jpg',
        ],
        [
            'name'      => "Edilbay qo'chqori — yirik",
            'desc'      => "Yirik Edilbay qo'chqori, 5 yoshli, 100 kg. Cho'l sharoitiga moslashgan. Sog'lom.",
            'category'  => "Qo'y", 'color' => "Qo'ng'ir", 'gender' => 'erkak',
            'age' => 5, 'weight' => 100, 'price' => 6_000_000,
            'folder' => 'qoylar', 'file' => 'images.jpeg',
        ],
        [
            'name'      => "Qo'zilar — 3 oylik",
            'desc'      => "3 oylik karakul qo'zilari. Sog'lom, barcha emlashlar qilingan. Tez o'sadi.",
            'category'  => "Qo'y", 'color' => 'Qora', 'gender' => 'urgochi',
            'age' => 1, 'weight' => 25, 'price' => 1_500_000,
            'folder' => 'qoylar', 'file' => 'imag2e.jpg',
        ],
    ];

    // Qoraqalpog'iston 14 tumani — har bir e'lon alohida tumanda
    private array $districts = [
        'Nukus shahri'       => [42.460, 59.613],
        'Amudaryo tumani'    => [41.433, 60.383],
        'Beruniy tumani'     => [41.697, 60.749],
        'Chimboy tumani'     => [42.942, 59.786],
        "Ellikqal'a tumani"  => [41.554, 61.864],
        'Kegeyli tumani'     => [42.784, 59.608],
        "Mo'ynoq tumani"     => [43.768, 59.017],
        'Nukus tumani'       => [42.560, 59.900],
        "Qonliko'l tumani"   => [42.858, 60.297],
        "Qorao'zak tumani"   => [43.046, 60.018],
        'Shumanay tumani'    => [42.200, 59.350],
        "Taxtako'pir tumani" => [42.917, 60.017],
        "To'rtko'l tumani"   => [41.484, 61.008],
        "Xo'jayli tumani"    => [41.975, 60.364],
    ];

    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('products');
        Storage::disk('public')->makeDirectory('products');

        $user     = User::first();
        $statusId = Status::where('name', 'Faol')->value('id') ?? Status::first()->id;
        $region   = Region::where('name', "Qoraqalpog'iston Respublikasi")->first();

        if (! $region) {
            $this->command->error("Qoraqalpog'iston Respublikasi topilmadi!");
            return;
        }

        $districtNames = array_keys($this->districts);

        foreach ($this->templates as $idx => $tmpl) {
            $districtName = $districtNames[$idx];
            $coords       = $this->districts[$districtName];

            $city = City::where('region_id', $region->id)
                        ->where('name', $districtName)
                        ->first()
                    ?? City::where('region_id', $region->id)->inRandomOrder()->first();

            $category = Category::where('name', $tmpl['category'])->first()
                        ?? Category::whereNotNull('parent_id')->first();
            $color    = Color::where('name', $tmpl['color'])->first() ?? Color::first();

            $imgPath = $this->copyLocalImage($tmpl['folder'], $tmpl['file'], $idx);

            Product::create([
                'name'          => $tmpl['name'],
                'description'   => $tmpl['desc'],
                'price'         => $tmpl['price'],
                'image'         => $imgPath,
                'category_id'   => $category->id,
                'user_id'       => $user->id,
                'color_id'      => $color->id,
                'age'           => $tmpl['age'],
                'weight'        => $tmpl['weight'],
                'region_id'     => $region->id,
                'city_id'       => $city->id,
                'status_id'     => $statusId,
                'gender'        => $tmpl['gender'],
                'contact_phone' => $user->phone,
                'latitude'      => round($coords[0] + mt_rand(-60, 60) / 1000, 7),
                'longitude'     => round($coords[1] + mt_rand(-60, 60) / 1000, 7),
            ]);

            $num = $idx + 1;
            $this->command->line("    ✓ [{$num}/14] {$tmpl['name']} — {$districtName}");
        }

        $this->command->info("  Jami 14 ta e'lon yaratildi.");
    }

    private function copyLocalImage(string $folder, string $file, int $idx): ?string
    {
        $srcPath  = base_path("{$folder}/{$file}");
        $ext      = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $destName = "products/{$folder}-{$idx}.{$ext}";

        if (! file_exists($srcPath)) {
            $this->command->warn("    ✗ Rasm topilmadi: {$srcPath}");
            return null;
        }

        Storage::disk('public')->put($destName, file_get_contents($srcPath));
        return $destName;
    }
}
