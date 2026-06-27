# ChorvaAI — Livestock Marketplace

O'zbekiston chorva mollari bozori. Foydalanuvchilar chorva mollarini sotish uchun e'lon joylashlari va sotib olish uchun qidirish imkoniyati.

## Tech Stack

- **Backend:** Laravel 11, PHP 8.3
- **Frontend:** Tailwind CSS, Vite
- **Database:** SQLite (yoki MySQL)
- **Auth:** Telefon raqam orqali (parol bilan)

## Arxitektura

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php       # Resource controller
│   │   └── ProfileController.php
│   └── Requests/
│       └── Product/
│           ├── StoreProductRequest.php
│           └── UpdateProductRequest.php
├── Models/
│   ├── Product.php
│   ├── Category.php
│   ├── Region.php / City.php
│   ├── Type.php / Color.php / Status.php
│   └── User.php
├── Policies/
│   └── ProductPolicy.php               # Faqat egasi tahrirlashi/o'chirishi mumkin
└── Services/
    └── ProductService.php              # Biznes mantiq
```

## O'rnatish

```bash
git clone https://github.com/Niyazbekdev/chorvaai.git
cd chorvaai

composer install
npm install

cp .env.example .env
php artisan key:generate

# SQLite uchun
touch database/database.sqlite

php artisan migrate --seed
npm run build
php artisan storage:link

php artisan serve
```

Brauzerda: `http://127.0.0.1:8000`

## Test foydalanuvchilar

Barcha parol: `password`

| Ism | Telefon |
|-----|---------|
| Admin | +998901234567 |
| Jasur Toshmatov | +998901111111 |
| Nodira Karimova | +998902222222 |
| Bobur Yo'ldoshev | +998903333333 |
| Malika Rahimova | +998904444444 |
| Sanjar Usmonov | +998905555555 |

## Asosiy imkoniyatlar

- **Marketplace** — chorva mollari e'lonlari ro'yxati (sotilganlar ko'rinmaydi)
- **Filter** — kategoriya (ierarxik), viloyat, narx oralig'i bo'yicha
- **E'lon CRUD** — faqat ro'yxatdan o'tgan foydalanuvchilar; faqat egasi tahrirlashi/o'chirishi mumkin
- **Profil** — `Mening e'lonlarim` bo'limi bilan
- **Seed** — `php artisan migrate:fresh --seed` har safar 25 ta mahsulot (rasmlar bilan) yaratadi

## Kategoriyalar

```
Qoramol
  └── Sigir, Buqa, Buzoq
Qo'y va echki
  └── Qo'y, Echki
Ot va tuya
  └── Ot, Tuya
```

## Muhim buyruqlar

```bash
# Bazani tozalab qayta seed qilish
php artisan migrate:fresh --seed

# CSS qayta build
npm run build

# Dev rejim (hot reload)
npm run dev
```
