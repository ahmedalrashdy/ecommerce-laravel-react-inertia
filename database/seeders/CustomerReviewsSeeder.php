<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class CustomerReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = $this->loadReviews();
        $users = $this->users();
        $products = Product::query()->orderBy('id')->get();

        if ($products->isEmpty()) {
            throw new RuntimeException('No products found. Seed products before adding reviews.');
        }

        foreach ($users as $index => $userData) {
            $user = User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'gender' => $userData['gender'],
                    'password' => $userData['password'],
                    'is_active' => true,
                    'is_admin' => false,
                    'reset_password_required' => false,
                ],
            );

            $product = $products[$index % $products->count()];
            $comment = $reviews[$index % count($reviews)];
            $rating = $index % 3 === 0 ? 5 : 4;

            Review::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ],
                [
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_approved' => true,
                ],
            );
        }
    }

    /**
     * @return array<int, string>
     */
    private function loadReviews(): array
    {
        $path = base_path('database/seed-data/reviews.json');

        if (! is_file($path)) {
            throw new RuntimeException('Reviews file not found: '.$path);
        }

        $reviews = json_decode(file_get_contents($path), true);

        if (! is_array($reviews) || count($reviews) < 20) {
            throw new RuntimeException('reviews.json must contain at least 20 items.');
        }

        return array_values($reviews);
    }

    /**
     * @return array<int, array{name: string, gender: string|null, email: string, password: string}>
     */
    private function users(): array
    {
        return [
            ['name' => 'سارة العتيبي', 'gender' => 'female', 'email' => 'sara.alotaibi@example.com', 'password' => 'Password123!'],
            ['name' => 'محمد الزهراني', 'gender' => 'male', 'email' => 'mohammed.alzahrani@example.com', 'password' => 'Password123!'],
            ['name' => 'نورة القحطاني', 'gender' => 'female', 'email' => 'noura.alqahtani@example.com', 'password' => 'Password123!'],
            ['name' => 'خالد العتيبي', 'gender' => 'male', 'email' => 'khaled.alotaibi@example.com', 'password' => 'Password123!'],
            ['name' => 'ريم السبيعي', 'gender' => 'female', 'email' => 'reem.alsubai@example.com', 'password' => 'Password123!'],
            ['name' => 'فيصل الغامدي', 'gender' => 'male', 'email' => 'faisal.alghamdi@example.com', 'password' => 'Password123!'],
            ['name' => 'لمى الشهري', 'gender' => 'female', 'email' => 'lama.alshahri@example.com', 'password' => 'Password123!'],
            ['name' => 'عبدالله الحربي', 'gender' => 'male', 'email' => 'abdullah.alharbi@example.com', 'password' => 'Password123!'],
            ['name' => 'أمل الدوسري', 'gender' => 'female', 'email' => 'amal.aldosari@example.com', 'password' => 'Password123!'],
            ['name' => 'عبدالرحمن الشهري', 'gender' => 'male', 'email' => 'abdulrahman.alshahri@example.com', 'password' => 'Password123!'],
            ['name' => 'هند المطيري', 'gender' => 'female', 'email' => 'hind.almotairi@example.com', 'password' => 'Password123!'],
            ['name' => 'يوسف العلي', 'gender' => 'male', 'email' => 'yousef.alali@example.com', 'password' => 'Password123!'],
            ['name' => 'جود السحيمي', 'gender' => 'female', 'email' => 'joud.alsuhaimi@example.com', 'password' => 'Password123!'],
            ['name' => 'سلمان الحربي', 'gender' => 'male', 'email' => 'salman.alharbi@example.com', 'password' => 'Password123!'],
            ['name' => 'دانة الشمري', 'gender' => 'female', 'email' => 'dana.alshammari@example.com', 'password' => 'Password123!'],
            ['name' => 'ماجد العبدالله', 'gender' => 'male', 'email' => 'majed.alabdullah@example.com', 'password' => 'Password123!'],
            ['name' => 'مها الغامدي', 'gender' => 'female', 'email' => 'maha.alghamdi@example.com', 'password' => 'Password123!'],
            ['name' => 'راشد الزهراني', 'gender' => 'male', 'email' => 'rashid.alzahrani@example.com', 'password' => 'Password123!'],
            ['name' => 'العنود القحطاني', 'gender' => 'female', 'email' => 'alanoud.alqahtani@example.com', 'password' => 'Password123!'],
            ['name' => 'عمر السالم', 'gender' => 'male', 'email' => 'omar.alsalem@example.com', 'password' => 'Password123!'],
        ];
    }
}
