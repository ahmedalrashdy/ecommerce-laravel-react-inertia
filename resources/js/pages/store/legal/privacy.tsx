import { Button } from '@/components/ui/button';
import {
    BottomCTA,
    ContactBadge,
    ContentBullet,
    ContentCard,
    FooterCTA,
    HeroBadge,
    HeroSection,
    InfoBox,
    PageLayout,
    TableOfContents,
} from '@/features/support-and-legal/components';
import {
    ASSETS,
    BRAND_DATA,
} from '@/features/support-and-legal/constants/brand-data';
import { formatDate } from '@/features/support-and-legal/utils/format-date';
import StoreLayout from '@/layouts/StoreLayout';
import { Head, Link } from '@inertiajs/react';
import { Mail, Shield } from 'lucide-react';

const sections = [
    {
        id: 'intro',
        title: 'مقدمة',
        body: (
            <>
                <p className="text-sm leading-7 text-muted-foreground">
                    توضّح هذه السياسة كيفية جمع واستخدام ومشاركة وحماية بياناتك
                    الشخصية عند استخدامك لموقع{' '}
                    <span className="font-semibold text-foreground">
                        {BRAND_DATA.website}
                    </span>{' '}
                    وخدمات {BRAND_DATA.nameAr}. باستخدامك للموقع، فأنت توافق على
                    الممارسات الموضحة في هذه السياسة.
                </p>
            </>
        ),
    },
    {
        id: 'data-we-collect',
        title: 'البيانات التي نجمعها',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="بيانات الحساب"
                    desc="مثل الاسم، البريد الإلكتروني، وكلمة المرور (مشفّرة)."
                />
                <ContentBullet
                    title="بيانات الطلبات"
                    desc="مثل المنتجات التي اشتريتها، مبالغ الدفع، حالة الطلب، وسجل التحديثات."
                />
                <ContentBullet
                    title="بيانات الشحن"
                    desc="مثل اسم المستلم، رقم الهاتف، العنوان، والمدينة/الرمز البريدي."
                />
                <ContentBullet
                    title="بيانات الدفع"
                    desc="قد يتم معالجة الدفع عبر مزودي خدمات الدفع. لا نقوم عادةً بتخزين بيانات البطاقة كاملة على خوادمنا."
                />
                <ContentBullet
                    title="بيانات الدعم والتواصل"
                    desc="مثل محتوى الرسائل، مرفقات الدعم، وسجلات التواصل لأغراض التحسين والمتابعة."
                />
                <ContentBullet
                    title="بيانات الاستخدام"
                    desc="مثل معلومات الجهاز والمتصفح، الصفحات التي تزورها، وبيانات تقريبة لتحسين الأداء وتجربة الاستخدام."
                />
            </div>
        ),
    },
    {
        id: 'how-we-use',
        title: 'كيف نستخدم بياناتك',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="تنفيذ الطلبات"
                    desc="لتجهيز الطلب وشحنه، وإصدار الإشعارات المتعلقة بحالته."
                />
                <ContentBullet
                    title="الدعم وخدمة العملاء"
                    desc="للرد على الاستفسارات وحل المشكلات ومعالجة طلبات الإرجاع/الضمان."
                />
                <ContentBullet
                    title="تحسين الخدمة"
                    desc="لتحليل الأداء، وتطوير تجربة التصفح والبحث، وتحسين جودة المحتوى."
                />
                <ContentBullet
                    title="الامتثال القانوني"
                    desc="للوفاء بالالتزامات النظامية ومكافحة الاحتيال وحماية حقوقنا وحقوق العملاء."
                />
            </div>
        ),
    },
    {
        id: 'legal-basis',
        title: 'الأساس النظامي للمعالجة',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    نعالج بياناتك لأسباب تتضمن: تنفيذ عقد الشراء (الطلب)،
                    الامتثال للالتزامات النظامية، مصالحنا المشروعة (مثل تحسين
                    الخدمة ومنع الاحتيال)، أو موافقتك حيث يلزم ذلك (مثل الاشتراك
                    في رسائل تسويقية إن كانت مفعلة).
                </p>
            </div>
        ),
    },
    {
        id: 'sharing',
        title: 'متى نشارك بياناتك',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="مزودو الدفع"
                    desc="لإتمام عمليات الدفع والتحقق من المعاملات."
                />
                <ContentBullet
                    title="شركات الشحن"
                    desc="لتسليم الطلبات وتوفير رقم تتبع وتحديثات التوصيل."
                />
                <ContentBullet
                    title="مزودو الاستضافة والتحليلات"
                    desc="لتشغيل الموقع وتحسين الأداء، وفق اتفاقيات حماية البيانات."
                />
                <ContentBullet
                    title="الالتزام القانوني"
                    desc="عند طلب ذلك من جهة نظامية مختصة، أو لحماية حقوقنا ومنع الاحتيال."
                />
                <p className="text-sm leading-7 text-muted-foreground">
                    لا نبيع بياناتك الشخصية لأي طرف ثالث.
                </p>
            </div>
        ),
    },
    {
        id: 'cookies',
        title: 'ملفات تعريف الارتباط والتقنيات المشابهة',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    قد نستخدم ملفات تعريف الارتباط (Cookies) وتقنيات مشابهة
                    لتحسين تجربتك (مثل تذكر الجلسة أو تفضيلات العرض)، وتحليل
                    استخدام الموقع، وتعزيز الأمان.
                </p>
                <p>
                    يمكنك التحكم في ملفات تعريف الارتباط من خلال إعدادات
                    المتصفح. تعطيل بعض الملفات قد يؤثر على وظائف مثل تسجيل
                    الدخول أو سلة التسوق.
                </p>
            </div>
        ),
    },
    {
        id: 'security',
        title: 'أمن البيانات',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    نطبّق إجراءات تقنية وتنظيمية مناسبة لحماية بياناتك من الوصول
                    غير المصرح به أو التعديل أو الإفصاح أو الإتلاف. ومع ذلك، لا
                    يمكن ضمان أمان أي نقل للبيانات عبر الإنترنت بنسبة 100%.
                </p>
                <p>
                    ننصح باستخدام كلمة مرور قوية وعدم مشاركتها، وتسجيل الخروج من
                    الأجهزة المشتركة.
                </p>
            </div>
        ),
    },
    {
        id: 'retention',
        title: 'مدة الاحتفاظ بالبيانات',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    نحتفظ بالبيانات طالما كان ذلك ضرورياً لتقديم الخدمة، وتنفيذ
                    الطلبات، والوفاء بالالتزامات النظامية، وحل النزاعات، وتطبيق
                    اتفاقياتنا. قد تختلف مدة الاحتفاظ حسب نوع البيانات وطبيعة
                    الطلب والمتطلبات النظامية.
                </p>
            </div>
        ),
    },
    {
        id: 'your-rights',
        title: 'حقوقك وخياراتك',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="الوصول والتصحيح"
                    desc="يمكنك طلب الوصول إلى بياناتك أو تحديثها إذا كانت غير دقيقة."
                />
                <ContentBullet
                    title="الحذف/التقييد"
                    desc="يمكنك طلب حذف بياناتك أو تقييد معالجتها في حدود ما تسمح به الأنظمة."
                />
                <ContentBullet
                    title="الاعتراض"
                    desc="يمكنك الاعتراض على بعض أنواع المعالجة وفقاً للأساس النظامي."
                />
                <ContentBullet
                    title="إلغاء الاشتراك"
                    desc="إذا كانت الرسائل التسويقية مفعلة، يمكنك إلغاء الاشتراك عبر الرابط داخل الرسالة أو عبر التواصل معنا."
                />
                <p className="text-sm leading-7 text-muted-foreground">
                    لممارسة حقوقك، تواصل معنا عبر{' '}
                    <a
                        className="font-semibold text-primary hover:underline"
                        href={`mailto:${BRAND_DATA.privacyEmail}`}
                    >
                        {BRAND_DATA.privacyEmail}
                    </a>
                    .
                </p>
            </div>
        ),
    },
    {
        id: 'children',
        title: 'خصوصية الأطفال',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    خدماتنا موجهة للجمهور العام وليست مخصصة للأطفال. إذا كنت
                    تعتقد أننا جمعنا بيانات شخصية لطفل دون قصد، يرجى التواصل
                    معنا لاتخاذ الإجراء المناسب.
                </p>
            </div>
        ),
    },
    {
        id: 'international',
        title: 'نقل البيانات عبر الحدود',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    قد تتم معالجة بعض البيانات عبر مزودين تقنيين أو خدمات
                    استضافة خارج بلدك، مع تطبيق ضمانات مناسبة لحماية البيانات
                    حسب الإمكان والمتطلبات النظامية.
                </p>
            </div>
        ),
    },
    {
        id: 'changes',
        title: 'تحديثات سياسة الخصوصية',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    قد نقوم بتحديث هذه السياسة من وقت لآخر. سننشر النسخة المحدثة
                    على هذه الصفحة مع تعديل تاريخ "آخر تحديث". استمرار استخدامك
                    للموقع بعد نشر التحديثات يعني قبولك لها.
                </p>
            </div>
        ),
    },
    {
        id: 'contact',
        title: 'التواصل معنا',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    للاستفسارات المتعلقة بالخصوصية أو طلبات الحقوق، تواصل معنا
                    عبر البريد:
                </p>
                <InfoBox>
                    <p className="text-sm font-bold text-foreground">
                        مسؤول الخصوصية
                    </p>
                    <p className="mt-1 text-sm text-muted-foreground">
                        {BRAND_DATA.nameAr}
                    </p>
                    <p className="mt-3 text-sm">
                        <a
                            href={`mailto:${BRAND_DATA.privacyEmail}`}
                            className="font-semibold text-primary hover:underline"
                        >
                            {BRAND_DATA.privacyEmail}
                        </a>
                    </p>
                    <p className="mt-2 text-xs text-muted-foreground">
                        للدعم العام والطلبات:{' '}
                        <a
                            href={`mailto:${BRAND_DATA.email}`}
                            className="font-semibold text-primary hover:underline"
                        >
                            {BRAND_DATA.email}
                        </a>
                    </p>
                </InfoBox>
            </div>
        ),
    },
];

export default function PrivacyPage() {
    return (
        <StoreLayout>
            <Head title={`سياسة الخصوصية | ${BRAND_DATA.nameAr}`}>
                <meta
                    name="description"
                    content="سياسة الخصوصية الخاصة بزين ماركت: ما البيانات التي نجمعها، وكيف نستخدمها، وحقوقك وخياراتك، وكيفية التواصل معنا."
                />
            </Head>

            <HeroSection
                icon={Shield}
                nameAr={BRAND_DATA.nameAr}
                nameEn={BRAND_DATA.nameEn}
                title="سياسة الخصوصية"
                description="نلتزم بحماية بياناتك الشخصية وتوضيح كيفية استخدامها بشكل شفاف."
                backgroundImage={ASSETS.heroBg}
                badges={
                    <>
                        <HeroBadge>
                            آخر تحديث: {formatDate(BRAND_DATA.lastUpdated)}
                        </HeroBadge>
                        <HeroBadge>المقر: {BRAND_DATA.location}</HeroBadge>
                        <HeroBadge href="/help">مركز المساعدة</HeroBadge>
                        <HeroBadge href="/contact">اتصل بنا</HeroBadge>
                    </>
                }
            />

            <PageLayout
                sidebar={
                    <TableOfContents
                        sections={sections}
                        footer={
                            <InfoBox>
                                <p className="text-xs font-bold text-foreground">
                                    اختصار
                                </p>
                                <p className="mt-2 text-xs leading-5 text-muted-foreground">
                                    للاستخدامات العامة أو الطلبات، تواصل مع{' '}
                                    <a
                                        className="font-semibold text-primary hover:underline"
                                        href={`mailto:${BRAND_DATA.email}`}
                                    >
                                        {BRAND_DATA.email}
                                    </a>
                                    .
                                </p>
                                <p className="mt-2 text-xs leading-5 text-muted-foreground">
                                    لطلبات الخصوصية:{' '}
                                    <a
                                        className="font-semibold text-primary hover:underline"
                                        href={`mailto:${BRAND_DATA.privacyEmail}`}
                                    >
                                        {BRAND_DATA.privacyEmail}
                                    </a>
                                    .
                                </p>
                            </InfoBox>
                        }
                    />
                }
            >
                <ContentCard>
                    {sections.map((s, idx) => (
                        <div
                            key={s.id}
                            id={s.id}
                            className="scroll-mt-24"
                        >
                            <h2 className="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                                {s.title}
                            </h2>
                            <div className="mt-3">{s.body}</div>

                            {idx !== sections.length - 1 && (
                                <div className="my-8 h-px bg-border" />
                            )}
                        </div>
                    ))}
                </ContentCard>

                <BottomCTA
                    title="هل لديك طلب متعلق ببياناتك؟"
                    description="راسل مسؤول الخصوصية وسنراجع طلبك وفق الأطر النظامية المتاحة."
                    actions={
                        <>
                            <Button
                                asChild
                                size="sm"
                                variant="default"
                            >
                                <a href={`mailto:${BRAND_DATA.privacyEmail}`}>
                                    مراسلة الخصوصية
                                </a>
                            </Button>
                            <Button
                                asChild
                                size="sm"
                                variant="outline"
                            >
                                <Link href="/contact">الدعم العام</Link>
                            </Button>
                        </>
                    }
                />

                <p className="mt-6 text-center text-xs text-muted-foreground">
                    © {new Date().getFullYear()} {BRAND_DATA.nameAr}. جميع
                    الحقوق محفوظة.
                </p>
            </PageLayout>

            <FooterCTA
                title="تحتاج مساعدة؟"
                description="للاستفسارات العامة أو متابعة الطلبات، فريق الدعم جاهز لمساعدتك."
                brandName={BRAND_DATA.nameAr}
                primaryActions={
                    <Button
                        asChild
                        size="lg"
                        variant="secondary"
                    >
                        <Link href="/contact">اتصل بنا</Link>
                    </Button>
                }
                contactBadges={
                    <>
                        <ContactBadge
                            icon={Mail}
                            href={`mailto:${BRAND_DATA.email}`}
                        >
                            {BRAND_DATA.email}
                        </ContactBadge>
                        <ContactBadge
                            icon={Mail}
                            href={`mailto:${BRAND_DATA.privacyEmail}`}
                        >
                            {BRAND_DATA.privacyEmail}
                        </ContactBadge>
                    </>
                }
            />
        </StoreLayout>
    );
}
