<?php

namespace Database\Seeders;

use App\Enums\BrandStatus;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // روابط صور حقيقية للعلامات التجارية
        $brandImages = [
            'https://images.unsplash.com/photo-1611262588024-d12430b98920?w=400',
            'https://images.unsplash.com/photo-1614680376573-df3480f0c6ff?w=400',
            'https://images.unsplash.com/photo-1611262588024-d12430b98920?w=400',
            'https://images.unsplash.com/photo-1614680376573-df3480f0c6ff?w=400',
            'https://images.unsplash.com/photo-1611262588024-d12430b98920?w=400',
        ];

        $brands = [
            ['name' => 'سامسونج', 'description' => 'شركة كورية رائدة في مجال الإلكترونيات والتكنولوجيا'],
            ['name' => 'آبل', 'description' => 'شركة أمريكية متخصصة في الأجهزة الإلكترونية والبرمجيات'],
            ['name' => 'هواوي', 'description' => 'شركة صينية رائدة في مجال الاتصالات والتكنولوجيا'],
            ['name' => 'شاومي', 'description' => 'شركة صينية متخصصة في الهواتف الذكية والأجهزة الإلكترونية'],
            ['name' => 'Sony', 'description' => 'شركة يابانية رائدة في مجال الإلكترونيات والترفيه'],
            ['name' => 'LG', 'description' => 'شركة كورية متخصصة في الأجهزة الإلكترونية والأجهزة المنزلية'],
            ['name' => 'باناسونيك', 'description' => 'شركة يابانية رائدة في الأجهزة الإلكترونية'],
            ['name' => 'فيليبس', 'description' => 'شركة هولندية متخصصة في الأجهزة الإلكترونية والإضاءة'],
            ['name' => 'Bosch', 'description' => 'شركة ألمانية رائدة في الأجهزة المنزلية والأدوات'],
            ['name' => 'Siemens', 'description' => 'شركة ألمانية متخصصة في الأجهزة الكهربائية'],
            ['name' => 'Whirlpool', 'description' => 'شركة أمريكية رائدة في الأجهزة المنزلية'],
            ['name' => 'Electrolux', 'description' => 'شركة سويدية متخصصة في الأجهزة المنزلية'],
            ['name' => 'IKEA', 'description' => 'شركة سويدية رائدة في الأثاث والديكور'],
            ['name' => 'Zara', 'description' => 'علامة أزياء إسبانية عالمية'],
            ['name' => 'H&M', 'description' => 'علامة أزياء سويدية عالمية'],
            ['name' => 'Nike', 'description' => 'شركة أمريكية رائدة في الملابس والأحذية الرياضية'],
            ['name' => 'Adidas', 'description' => 'شركة ألمانية متخصصة في الملابس والأحذية الرياضية'],
            ['name' => 'Puma', 'description' => 'شركة ألمانية رائدة في الملابس الرياضية'],
            ['name' => 'Reebok', 'description' => 'شركة أمريكية متخصصة في الملابس الرياضية'],
            ['name' => 'Under Armour', 'description' => 'شركة أمريكية رائدة في الملابس الرياضية'],
            ['name' => 'Lacoste', 'description' => 'علامة أزياء فرنسية عالمية'],
            ['name' => 'Calvin Klein', 'description' => 'علامة أزياء أمريكية فاخرة'],
            ['name' => 'Tommy Hilfiger', 'description' => 'علامة أزياء أمريكية كلاسيكية'],
            ['name' => 'Levi\'s', 'description' => 'شركة أمريكية رائدة في صناعة الجينز'],
            ['name' => 'Gap', 'description' => 'علامة أزياء أمريكية شعبية'],
            ['name' => 'Uniqlo', 'description' => 'علامة أزياء يابانية عالمية'],
            ['name' => 'Mango', 'description' => 'علامة أزياء إسبانية عصرية'],
            ['name' => 'Massimo Dutti', 'description' => 'علامة أزياء إسبانية راقية'],
            ['name' => 'Pull & Bear', 'description' => 'علامة أزياء إسبانية شبابية'],
            ['name' => 'Bershka', 'description' => 'علامة أزياء إسبانية عصرية'],
            ['name' => 'Stradivarius', 'description' => 'علامة أزياء إسبانية أنثوية'],
            ['name' => 'Oysho', 'description' => 'علامة ملابس داخلية إسبانية'],
            ['name' => 'Victoria\'s Secret', 'description' => 'علامة ملابس داخلية أمريكية'],
            ['name' => 'Intimissimi', 'description' => 'علامة ملابس داخلية إيطالية'],
            ['name' => 'Chanel', 'description' => 'دار أزياء فرنسية فاخرة'],
            ['name' => 'Dior', 'description' => 'دار أزياء فرنسية راقية'],
            ['name' => 'Gucci', 'description' => 'دار أزياء إيطالية فاخرة'],
            ['name' => 'Prada', 'description' => 'دار أزياء إيطالية راقية'],
            ['name' => 'Versace', 'description' => 'دار أزياء إيطالية فاخرة'],
            ['name' => 'Armani', 'description' => 'دار أزياء إيطالية راقية'],
            ['name' => 'Rolex', 'description' => 'شركة سويسرية رائدة في صناعة الساعات الفاخرة'],
            ['name' => 'Omega', 'description' => 'شركة سويسرية متخصصة في الساعات الفاخرة'],
            ['name' => 'Tag Heuer', 'description' => 'شركة سويسرية رائدة في الساعات الرياضية'],
            ['name' => 'Casio', 'description' => 'شركة يابانية متخصصة في الساعات الإلكترونية'],
            ['name' => 'Seiko', 'description' => 'شركة يابانية رائدة في صناعة الساعات'],
            ['name' => 'Citizen', 'description' => 'شركة يابانية متخصصة في الساعات'],
            ['name' => 'Ray-Ban', 'description' => 'علامة نظارات أمريكية عالمية'],
            ['name' => 'Oakley', 'description' => 'شركة أمريكية متخصصة في النظارات الرياضية'],
            ['name' => 'Polaroid', 'description' => 'شركة أمريكية رائدة في النظارات الشمسية'],
            ['name' => 'Tiffany & Co.', 'description' => 'دار مجوهرات أمريكية فاخرة'],
        ];

        foreach ($brands as $index => $brand) {
            Brand::create([
                'name' => $brand['name'],
                'description' => $brand['description'],
                'image_path' => $brandImages[$index % count($brandImages)],
                'status' => BrandStatus::Published->value,
                'featured' => $index < 10, // أول 10 علامات تجارية مميزة
            ]);
        }

        $this->command->info('تم إنشاء 50 علامة تجارية بنجاح!');
    }
}
