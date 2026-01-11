<?php

namespace Database\Seeders;

use App\Enums\CategoryStatus;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // روابط صور حقيقية للفئات
        $categoryImages = [
            'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
            'https://images.unsplash.com/photo-1445205170230-053b83016050?w=800',
            'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=800',
            'https://images.unsplash.com/photo-1445205170230-053b83016050?w=800',
            'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
            'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
            'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
            'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
        ];

        // 15 فئة رئيسية (Parent Categories)
        $parentCategories = [
            ['name' => 'إلكترونيات', 'description' => 'جميع الأجهزة الإلكترونية والتقنية'],
            ['name' => 'ملابس وأزياء', 'description' => 'ملابس رجالية ونسائية وأطفال'],
            ['name' => 'منزل ومطبخ', 'description' => 'أدوات منزلية ومستلزمات المطبخ'],
            ['name' => 'أثاث', 'description' => 'أثاث منزلي ومكتبي'],
            ['name' => 'أجهزة كهربائية', 'description' => 'أجهزة منزلية كهربائية'],
            ['name' => 'ألعاب وترفيه', 'description' => 'ألعاب أطفال وبالغين'],
            ['name' => 'كتب ومكتبات', 'description' => 'كتب ومجلات وقرطاسية'],
            ['name' => 'رياضة ولياقة', 'description' => 'معدات رياضية وملابس رياضية'],
            ['name' => 'جمال والعناية', 'description' => 'مستحضرات تجميل وعناية شخصية'],
            ['name' => 'سيارات ودراجات', 'description' => 'قطع غيار وإكسسوارات سيارات'],
            ['name' => 'حيوانات أليفة', 'description' => 'طعام ومستلزمات الحيوانات الأليفة'],
            ['name' => 'أدوات ومعدات', 'description' => 'أدوات يدوية وكهربائية'],
            ['name' => 'ساعات ونظارات', 'description' => 'ساعات يد ونظارات شمسية وطبية'],
            ['name' => 'مجوهرات', 'description' => 'ذهب وفضة ومجوهرات'],
            ['name' => 'طعام ومشروبات', 'description' => 'أطعمة ومشروبات متنوعة'],
        ];

        $createdParents = [];

        foreach ($parentCategories as $index => $parent) {
            $category = Category::create([
                'name' => $parent['name'],
                'description' => $parent['description'],
                'image_path' => $categoryImages[$index % count($categoryImages)],
                'status' => CategoryStatus::Published->value,
            ]);

            $createdParents[] = $category;
        }

        // المستوى الثاني: كل فئة رئيسية لها 3-4 أبناء
        $level2Data = [
            // إلكترونيات (parent index 0)
            ['name' => 'هواتف ذكية', 'description' => 'هواتف محمولة وملحقاتها'],
            ['name' => 'حواسيب', 'description' => 'أجهزة كمبيوتر محمولة وسطحية'],
            ['name' => 'تلفزيونات', 'description' => 'شاشات تلفزيون وشاشات عرض'],
            ['name' => 'سماعات', 'description' => 'سماعات لاسلكية وسلكية'],

            // ملابس وأزياء (parent index 1)
            ['name' => 'ملابس رجالية', 'description' => 'ملابس رجالية عصرية'],
            ['name' => 'ملابس نسائية', 'description' => 'أزياء نسائية راقية'],
            ['name' => 'ملابس أطفال', 'description' => 'ملابس للأطفال والرضع'],
            ['name' => 'أحذية', 'description' => 'أحذية رجالية ونسائية'],

            // منزل ومطبخ (parent index 2)
            ['name' => 'أدوات المطبخ', 'description' => 'أدوات طبخ وطهي'],
            ['name' => 'ديكورات منزلية', 'description' => 'تحف وديكورات للبيت'],
            ['name' => 'مفروشات', 'description' => 'ستائر وفرشات'],
            ['name' => 'تنظيف', 'description' => 'منتجات تنظيف منزلية'],

            // أثاث (parent index 3)
            ['name' => 'أثاث غرف النوم', 'description' => 'أسرة وخزائن وطاولات'],
            ['name' => 'أثاث الصالون', 'description' => 'كنب وطاولات قهوة'],
            ['name' => 'أثاث مكتبي', 'description' => 'مكاتب وكراسي مكتبية'],

            // أجهزة كهربائية (parent index 4)
            ['name' => 'ثلاجات', 'description' => 'ثلاجات ومجمدات'],
            ['name' => 'غسالات', 'description' => 'غسالات ملابس وأطباق'],
            ['name' => 'مكيفات', 'description' => 'أجهزة تكييف وتبريد'],
            ['name' => 'أفران', 'description' => 'أفران وميكروويف'],

            // ألعاب وترفيه (parent index 5)
            ['name' => 'ألعاب أطفال', 'description' => 'ألعاب تعليمية وترفيهية'],
            ['name' => 'ألعاب فيديو', 'description' => 'ألعاب إلكترونية وكونسول'],
            ['name' => 'ألعاب خارجية', 'description' => 'ألعاب حدائق وملاعب'],

            // كتب ومكتبات (parent index 6)
            ['name' => 'كتب عربية', 'description' => 'كتب باللغة العربية'],
            ['name' => 'كتب إنجليزية', 'description' => 'كتب باللغة الإنجليزية'],
            ['name' => 'قرطاسية', 'description' => 'أدوات مكتبية وقرطاسية'],

            // رياضة ولياقة (parent index 7)
            ['name' => 'معدات رياضية', 'description' => 'أجهزة وأدوات رياضية'],
            ['name' => 'ملابس رياضية', 'description' => 'ملابس وأحذية رياضية'],
            ['name' => 'مكملات غذائية', 'description' => 'مكملات رياضية وبروتين'],

            // جمال والعناية (parent index 8)
            ['name' => 'مستحضرات تجميل', 'description' => 'مكياج ومستحضرات تجميل'],
            ['name' => 'عناية بالبشرة', 'description' => 'كريمات ومستحضرات عناية'],
            ['name' => 'عطور', 'description' => 'عطور رجالية ونسائية'],

            // سيارات ودراجات (parent index 9)
            ['name' => 'قطع غيار', 'description' => 'قطع غيار سيارات'],
            ['name' => 'إكسسوارات', 'description' => 'إكسسوارات سيارات'],
            ['name' => 'دراجات', 'description' => 'دراجات هوائية ونارية'],

            // حيوانات أليفة (parent index 10)
            ['name' => 'طعام حيوانات', 'description' => 'أطعمة للكلاب والقطط'],
            ['name' => 'مستلزمات', 'description' => 'ألعاب وأدوات للحيوانات'],

            // أدوات ومعدات (parent index 11)
            ['name' => 'أدوات يدوية', 'description' => 'مطارق ومفكات وأدوات'],
            ['name' => 'أدوات كهربائية', 'description' => 'مثاقب ومناشير'],

            // ساعات ونظارات (parent index 12)
            ['name' => 'ساعات يد', 'description' => 'ساعات رجالية ونسائية'],
            ['name' => 'نظارات', 'description' => 'نظارات شمسية وطبية'],

            // مجوهرات (parent index 13)
            ['name' => 'ذهب', 'description' => 'مجوهرات ذهبية'],
            ['name' => 'فضة', 'description' => 'مجوهرات فضية'],

            // طعام ومشروبات (parent index 14)
            ['name' => 'أطعمة جافة', 'description' => 'أرز ومعكرونة وحبوب'],
            ['name' => 'مشروبات', 'description' => 'مشروبات غازية وعصائر'],
            ['name' => 'مياه معدنية', 'description' => 'مياه شرب طبيعية'],
        ];

        // توزيع الفئات على الآباء (كل أب له 3 أبناء على الأقل)
        $parentDistribution = [3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3]; // عدد الأبناء لكل أب

        $createdLevel2 = [];
        $level2Index = 0;

        foreach ($createdParents as $parentIndex => $parent) {
            $childrenCount = $parentDistribution[$parentIndex];
            for ($i = 0; $i < $childrenCount; $i++) {
                $catData = $level2Data[$level2Index];
                $category = Category::create([
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'image_path' => $categoryImages[array_rand($categoryImages)],
                    'status' => CategoryStatus::Published->value,
                ]);
                $category->appendToNode($parent)->save();
                $createdLevel2[] = $category;
                $level2Index++;
            }

        }

        // المستوى الثالث: كل فئة من المستوى الثاني لها 2-3 فئات فرعية
        $level3Data = [
            // هواتف ذكية (index 0)
            ['name' => 'آيفون', 'description' => 'هواتف آيفون وملحقاتها'],
            ['name' => 'سامسونج', 'description' => 'هواتف سامسونج'],
            ['name' => 'شاومي', 'description' => 'هواتف شاومي'],

            // حواسيب (index 1)
            ['name' => 'لابتوب', 'description' => 'أجهزة كمبيوتر محمولة'],
            ['name' => 'كمبيوتر مكتبي', 'description' => 'أجهزة كمبيوتر سطحية'],
            ['name' => 'تابلت', 'description' => 'أجهزة لوحية'],

            // تلفزيونات (index 2)
            ['name' => 'شاشات LED', 'description' => 'شاشات LED عالية الجودة'],
            ['name' => 'شاشات OLED', 'description' => 'شاشات OLED متطورة'],

            // سماعات (index 3)
            ['name' => 'سماعات لاسلكية', 'description' => 'سماعات بلوتوث'],
            ['name' => 'سماعات سلكية', 'description' => 'سماعات سلكية'],

            // ملابس رجالية (index 4)
            ['name' => 'قمصان', 'description' => 'قمصان رجالية'],
            ['name' => 'بناطيل', 'description' => 'بناطيل رجالية'],
            ['name' => 'جاكيتات', 'description' => 'جاكيتات ومعاطف'],

            // ملابس نسائية (index 5)
            ['name' => 'فساتين', 'description' => 'فساتين نسائية'],
            ['name' => 'بلوزات', 'description' => 'بلوزات نسائية'],
            ['name' => 'تنانير', 'description' => 'تنانير نسائية'],

            // ملابس أطفال (index 6)
            ['name' => 'ملابس رضع', 'description' => 'ملابس للأطفال الرضع'],
            ['name' => 'ملابس أطفال', 'description' => 'ملابس للأطفال'],

            // أحذية (index 7)
            ['name' => 'أحذية رياضية', 'description' => 'أحذية رياضية'],
            ['name' => 'أحذية رسمية', 'description' => 'أحذية رسمية'],

            // أدوات المطبخ (index 8)
            ['name' => 'أواني طبخ', 'description' => 'قدور ومقالي'],
            ['name' => 'أدوات خبز', 'description' => 'أدوات خبز ومعجنات'],

            // ديكورات منزلية (index 9)
            ['name' => 'لوحات فنية', 'description' => 'لوحات ورسومات'],
            ['name' => 'شمعدانات', 'description' => 'شمعدانات وإضاءة'],

            // مفروشات (index 10)
            ['name' => 'ستائر', 'description' => 'ستائر منزلية'],
            ['name' => 'فرشات', 'description' => 'فرشات وأغطية'],

            // تنظيف (index 11)
            ['name' => 'منظفات', 'description' => 'منتجات تنظيف'],
            ['name' => 'إسفنج', 'description' => 'إسفنج ومناديل'],

            // أثاث غرف النوم (index 12)
            ['name' => 'أسرة', 'description' => 'أسرة ومراتب'],
            ['name' => 'خزائن', 'description' => 'خزائن ملابس'],

            // أثاث الصالون (index 13)
            ['name' => 'كنب', 'description' => 'كنب ومقاعد'],
            ['name' => 'طاولات', 'description' => 'طاولات قهوة وطعام'],

            // أثاث مكتبي (index 14)
            ['name' => 'مكاتب', 'description' => 'مكاتب عمل'],
            ['name' => 'كراسي', 'description' => 'كراسي مكتبية'],

            // ثلاجات (index 15)
            ['name' => 'ثلاجات كبيرة', 'description' => 'ثلاجات كبيرة الحجم'],
            ['name' => 'ثلاجات صغيرة', 'description' => 'ثلاجات صغيرة'],

            // غسالات (index 16)
            ['name' => 'غسالات ملابس', 'description' => 'غسالات ملابس'],
            ['name' => 'غسالات أطباق', 'description' => 'غسالات أطباق'],

            // مكيفات (index 17)
            ['name' => 'مكيفات شباك', 'description' => 'مكيفات شباك'],
            ['name' => 'مكيفات سبليت', 'description' => 'مكيفات سبليت'],

            // أفران (index 18)
            ['name' => 'أفران كهربائية', 'description' => 'أفران كهربائية'],
            ['name' => 'ميكروويف', 'description' => 'أفران ميكروويف'],

            // ألعاب أطفال (index 19)
            ['name' => 'ألعاب تعليمية', 'description' => 'ألعاب تعليمية'],
            ['name' => 'ألعاب حركية', 'description' => 'ألعاب حركية'],

            // ألعاب فيديو (index 20)
            ['name' => 'بلايستيشن', 'description' => 'ألعاب بلايستيشن'],
            ['name' => 'إكس بوكس', 'description' => 'ألعاب إكس بوكس'],

            // ألعاب خارجية (index 21)
            ['name' => 'ألعاب حدائق', 'description' => 'ألعاب حدائق'],
            ['name' => 'ألعاب ملاعب', 'description' => 'ألعاب ملاعب'],

            // كتب عربية (index 22)
            ['name' => 'روايات', 'description' => 'روايات عربية'],
            ['name' => 'كتب علمية', 'description' => 'كتب علمية عربية'],

            // كتب إنجليزية (index 23)
            ['name' => 'روايات إنجليزية', 'description' => 'روايات إنجليزية'],
            ['name' => 'كتب تعليمية', 'description' => 'كتب تعليمية إنجليزية'],

            // قرطاسية (index 24)
            ['name' => 'أدوات كتابة', 'description' => 'أقلام ودفاتر'],
            ['name' => 'مستلزمات مكتبية', 'description' => 'مستلزمات مكتبية'],

            // معدات رياضية (index 25)
            ['name' => 'أوزان', 'description' => 'أوزان ودمبلز'],
            ['name' => 'أجهزة كارديو', 'description' => 'أجهزة كارديو'],

            // ملابس رياضية (index 26)
            ['name' => 'ملابس جيم', 'description' => 'ملابس صالة ألعاب'],
            ['name' => 'ملابس ركض', 'description' => 'ملابس ركض'],

            // مكملات غذائية (index 27)
            ['name' => 'بروتين', 'description' => 'مكملات بروتين'],
            ['name' => 'فيتامينات', 'description' => 'مكملات فيتامينات'],

            // مستحضرات تجميل (index 28)
            ['name' => 'مكياج', 'description' => 'مستحضرات مكياج'],
            ['name' => 'أدوات مكياج', 'description' => 'فرش ومستحضرات'],

            // عناية بالبشرة (index 29)
            ['name' => 'كريمات', 'description' => 'كريمات عناية'],
            ['name' => 'ماسكات', 'description' => 'ماسكات للوجه'],

            // عطور (index 30)
            ['name' => 'عطور رجالية', 'description' => 'عطور رجالية'],
            ['name' => 'عطور نسائية', 'description' => 'عطور نسائية'],

            // قطع غيار (index 31)
            ['name' => 'محركات', 'description' => 'قطع محرك'],
            ['name' => 'إطارات', 'description' => 'إطارات سيارات'],

            // إكسسوارات (index 32)
            ['name' => 'مقاعد', 'description' => 'مقاعد سيارات'],
            ['name' => 'إضاءة', 'description' => 'إضاءة سيارات'],

            // دراجات (index 33)
            ['name' => 'دراجات هوائية', 'description' => 'دراجات هوائية'],
            ['name' => 'دراجات نارية', 'description' => 'دراجات نارية'],

            // طعام حيوانات (index 34)
            ['name' => 'طعام كلاب', 'description' => 'أطعمة للكلاب'],
            ['name' => 'طعام قطط', 'description' => 'أطعمة للقطط'],

            // مستلزمات (index 35)
            ['name' => 'ألعاب حيوانات', 'description' => 'ألعاب للحيوانات'],
            ['name' => 'أدوات رعاية', 'description' => 'أدوات رعاية الحيوانات'],

            // أدوات يدوية (index 36)
            ['name' => 'مطارق', 'description' => 'مطارق يدوية'],
            ['name' => 'مفكات', 'description' => 'مفكات براغي'],

            // أدوات كهربائية (index 37)
            ['name' => 'مثاقب', 'description' => 'مثاقب كهربائية'],
            ['name' => 'مناشير', 'description' => 'مناشير كهربائية'],

            // ساعات يد (index 38)
            ['name' => 'ساعات رجالية', 'description' => 'ساعات يد رجالية'],
            ['name' => 'ساعات نسائية', 'description' => 'ساعات يد نسائية'],

            // نظارات (index 39)
            ['name' => 'نظارات شمسية', 'description' => 'نظارات شمسية'],
            ['name' => 'نظارات طبية', 'description' => 'نظارات طبية'],

            // ذهب (index 40)
            ['name' => 'خواتم', 'description' => 'خواتم ذهبية'],
            ['name' => 'أساور', 'description' => 'أساور ذهبية'],

            // فضة (index 41)
            ['name' => 'خواتم فضية', 'description' => 'خواتم فضية'],
            ['name' => 'أساور فضية', 'description' => 'أساور فضية'],

            // أطعمة جافة (index 42)
            ['name' => 'أرز', 'description' => 'أرز بجميع أنواعه'],
            ['name' => 'معكرونة', 'description' => 'معكرونة'],

            // مشروبات (index 43)
            ['name' => 'مشروبات غازية', 'description' => 'مشروبات غازية'],
            ['name' => 'عصائر', 'description' => 'عصائر طبيعية'],
        ];

        // توزيع الفئات على المستوى الثاني
        // 15 أب * 3 أبناء = 45 فئة من المستوى الثاني
        // نحتاج 60 فئة من المستوى الثالث (120 - 15 - 45 = 60)
        // توزيع: 20 فئة من المستوى الثاني لها 3 أبناء (60 فئة)
        // الباقي (25 فئة) ليس لها أبناء للوصول إلى 120 بالضبط
        // لكن لتلبية "كل ابن له فئات على الأقل 3"، سنعطي 20 فئة 3 أبناء
        $level2ChildrenCount = [3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        $level3Index = 0;

        foreach ($createdLevel2 as $level2Index => $level2Category) {
            $childrenCount = $level2ChildrenCount[$level2Index] ?? 0;
            if ($childrenCount > 0 && $level3Index < count($level3Data)) {
                for ($i = 0; $i < $childrenCount && $level3Index < count($level3Data); $i++) {
                    $catData = $level3Data[$level3Index];
                    $category = Category::create([
                        'name' => $catData['name'],
                        'description' => $catData['description'],
                        'image_path' => $categoryImages[array_rand($categoryImages)],
                        'status' => CategoryStatus::Published->value,
                    ]);
                    $category->appendToNode($level2Category)->save();
                    $level3Index++;
                }
            }
        }

        $totalCategories = count($createdParents) + count($createdLevel2) + $level3Index;
        $this->command->info("تم إنشاء {$totalCategories} فئة بنجاح!");
    }
}
