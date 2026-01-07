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
import { FileText, Mail } from 'lucide-react';
import React from 'react';

const sections = [
    {
        id: 'intro',
        title: 'مقدمة ونطاق التطبيق',
        body: (
            <div className="space-y-3">
                <p className="text-sm leading-7 text-muted-foreground">
                    تحكم هذه الشروط والأحكام استخدامك لموقع{' '}
                    <span className="font-semibold text-foreground">
                        {BRAND_DATA.website}
                    </span>{' '}
                    وخدمات {BRAND_DATA.nameAr} بما في ذلك تصفح المنتجات، إنشاء
                    الحساب، تقديم الطلبات، وأي تواصل معنا. باستخدامك للموقع، فإنك
                    توافق على هذه الشروط.
                </p>
                <p className="text-sm leading-7 text-muted-foreground">
                    إذا لم توافق على أي جزء من هذه الشروط، يرجى التوقف عن
                    استخدام الموقع.
                </p>
            </div>
        ),
    },
    {
        id: 'definitions',
        title: 'تعريفات',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="الموقع"
                    desc={`موقع ${BRAND_DATA.website} وتطبيقاته/واجهاته التابعة لـ ${BRAND_DATA.nameAr}.`}
                />
                <ContentBullet
                    title="العميل/المستخدم"
                    desc="أي شخص يقوم بتصفح الموقع أو إنشاء حساب أو تنفيذ عملية شراء."
                />
                <ContentBullet
                    title="الطلب"
                    desc="عملية شراء يقدّمها العميل وتتضمن منتجات ومبالغ دفع وعنوان شحن."
                />
                <ContentBullet
                    title="المنتجات"
                    desc="السلع المعروضة في الموقع بما في ذلك المتغيرات (مثل اللون/السعة)."
                />
            </div>
        ),
    },
    {
        id: 'eligibility',
        title: 'الأهلية واستخدام الموقع',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    يجب أن تكون لديك الأهلية النظامية لإبرام عقود الشراء وفق
                    الأنظمة المعمول بها. أنت مسؤول عن صحة معلوماتك عند التسجيل
                    أو إتمام الطلب.
                </p>
                <p>
                    يحظر استخدام الموقع لأي أغراض غير مشروعة أو إساءة الاستخدام
                    أو محاولة الوصول غير المصرح به لأنظمة الموقع.
                </p>
            </div>
        ),
    },
    {
        id: 'account',
        title: 'الحساب وكلمة المرور',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="مسؤولية الحساب"
                    desc="أنت مسؤول عن الحفاظ على سرية بيانات الدخول وعن أي نشاط يتم عبر حسابك."
                />
                <ContentBullet
                    title="دقة البيانات"
                    desc="يجب أن تكون بياناتك (الاسم/العنوان/رقم الهاتف) دقيقة لتفادي تأخير الشحن أو فشل التسليم."
                />
                <ContentBullet
                    title="تعليق/إيقاف الحساب"
                    desc="قد نعلّق أو نوقف الحساب عند الاشتباه في احتيال أو انتهاك لهذه الشروط أو إساءة استخدام المنصة."
                />
            </div>
        ),
    },
    {
        id: 'products',
        title: 'المنتجات والمواصفات والتوفر',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    نبذل جهداً معقولاً لعرض معلومات دقيقة عن المنتجات
                    (المواصفات، الصور، السعر). قد تظهر فروقات طفيفة في الألوان
                    بسبب إعدادات الشاشة.
                </p>
                <p>
                    التوفر يعتمد على المخزون. قد يتم إلغاء منتج أو متغير من طلبك
                    في حال نفاد المخزون بعد إتمام الطلب، وفي هذه الحالة سنقوم
                    بإبلاغك وإجراء التسوية المناسبة وفق الحالة.
                </p>
            </div>
        ),
    },
    {
        id: 'pricing',
        title: 'الأسعار والضرائب',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    الأسعار المعروضة تشمل/لا تشمل الضريبة حسب ما يظهر أثناء
                    إتمام الطلب. قد تُضاف تكلفة الشحن أو رسوم أخرى وفقاً لمدينة
                    التسليم وطريقة الشحن.
                </p>
                <p>
                    في حال وجود خطأ تسعيري واضح، نحتفظ بالحق في تصحيح السعر قبل
                    الشحن، مع إتاحة خيار الإلغاء للعميل إذا لم يرغب بالاستمرار.
                </p>
            </div>
        ),
    },
    {
        id: 'orders-payments',
        title: 'الطلبات والدفع',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="تأكيد الطلب"
                    desc="يُعتبر الطلب مؤكداً بعد نجاح عملية الدفع/التفويض وظهور الطلب في حسابك مع رقم طلب."
                />
                <ContentBullet
                    title="طرق الدفع"
                    desc="تتوفر طرق الدفع بحسب ما يظهر في شاشة الدفع. قد تتم المعالجة عبر مزودي دفع خارجيين وفق شروطهم."
                />
                <ContentBullet
                    title="مكافحة الاحتيال"
                    desc="قد نطلب تحققاً إضافياً لبعض الطلبات لحماية العميل والمنصة. قد ينتج عن ذلك تأخير مؤقت."
                />
                <ContentBullet
                    title="الإلغاء"
                    desc="يمكن إلغاء الطلب قبل بدء التجهيز للشحن. بعد بدء التجهيز قد لا يكون الإلغاء ممكناً ويطبق مسار الإرجاع."
                />
            </div>
        ),
    },
    {
        id: 'shipping',
        title: 'الشحن والتسليم',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="المدة"
                    desc="التقديرات الزمنية للشحن إرشادية وقد تتأثر بمواسم الضغط أو ظروف شركات الشحن."
                />
                <ContentBullet
                    title="العنوان"
                    desc="مسؤوليتك إدخال عنوان صحيح. الأخطاء قد تسبب تأخيراً أو فشل تسليم ورسوم إعادة شحن."
                />
                <ContentBullet
                    title="الاستلام"
                    desc="قد يتطلب التسليم وجود المستلم أو من ينوب عنه. في حال عدم الاستلام قد تعيد شركة الشحن المحاولة وفق سياستها."
                />
                <p className="text-sm leading-7 text-muted-foreground">
                    راجع سياسة الشحن للتفاصيل:{' '}
                    <Link
                        className="font-semibold text-primary hover:underline"
                        href="/shipping"
                    >
                        سياسة الشحن والتسليم
                    </Link>
                    .
                </p>
            </div>
        ),
    },
    {
        id: 'returns',
        title: 'الإرجاع والاستبدال',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    تخضع طلبات الإرجاع والاستبدال لسياسة الإرجاع المعتمدة بالموقع،
                    بما في ذلك المدة المتاحة، حالة المنتج، ووجود الملحقات
                    والتغليف. قد تختلف شروط بعض المنتجات حسب طبيعتها.
                </p>
                <p>
                    عند استلام المرتجع، يتم الفحص واتخاذ القرار
                    (استرجاع/استبدال/رفض) وفق السياسة وحالة المنتج.
                </p>
                <p>
                    التفاصيل:{' '}
                    <Link
                        className="font-semibold text-primary hover:underline"
                        href="/returns"
                    >
                        سياسة الإرجاع والاستبدال
                    </Link>
                    .
                </p>
            </div>
        ),
    },
    {
        id: 'warranty',
        title: 'الضمان',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    الضمان يخضع لشروط الشركة المصنعة أو مزود الضمان، ويغطي عادةً
                    عيوب التصنيع. لا يغطي الضمان غالباً الأضرار الناتجة عن سوء
                    الاستخدام أو الكسر أو السوائل ما لم يُنص خلاف ذلك.
                </p>
                <p>
                    التفاصيل:{' '}
                    <Link
                        className="font-semibold text-primary hover:underline"
                        href="/warranty"
                    >
                        سياسة الضمان
                    </Link>
                    .
                </p>
            </div>
        ),
    },
    {
        id: 'reviews',
        title: 'المراجعات والمحتوى الذي يقدمه المستخدم',
        body: (
            <div className="space-y-3">
                <ContentBullet
                    title="محتوى المراجعات"
                    desc="يجب أن يكون المحتوى دقيقاً ومحترماً وغير مخالف للأنظمة أو حقوق الغير."
                />
                <ContentBullet
                    title="الإشراف"
                    desc="قد نقوم بمراجعة/إخفاء/حذف أي محتوى يخالف هذه الشروط أو يشتبه بأنه مضلل أو مسيء."
                />
                <ContentBullet
                    title="الترخيص"
                    desc="بإرسال مراجعة أو تعليق، تمنحنا حقاً غير حصري لعرضه داخل المنصة لأغراض التشغيل."
                />
            </div>
        ),
    },
    {
        id: 'intellectual-property',
        title: 'الملكية الفكرية',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    جميع المحتويات على الموقع (النصوص، الشعارات، التصاميم، الصور،
                    الكود) مملوكة لـ {BRAND_DATA.nameAr} أو مرخصة لها، ومحمية
                    بموجب الأنظمة ذات الصلة.
                </p>
                <p>
                    لا يجوز نسخ أو إعادة نشر أو استغلال أي محتوى دون إذن كتابي
                    مسبق إلا في حدود الاستخدام الشخصي غير التجاري المسموح به.
                </p>
            </div>
        ),
    },
    {
        id: 'liability',
        title: 'حدود المسؤولية وإخلاء المسؤولية',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    نسعى لتقديم خدمة مستقرة ومعلومات دقيقة، لكن قد تحدث أخطاء
                    تقنية أو انقطاعات أو أخطاء بشرية. يتم تقديم الخدمات "كما هي"
                    بقدر ما يسمح به النظام.
                </p>
                <p>
                    إلى الحد الذي يسمح به النظام، لا نكون مسؤولين عن أي أضرار غير
                    مباشرة أو تبعية أو فقدان أرباح ناتجة عن استخدام الموقع أو عدم
                    القدرة على استخدامه، أو عن تأخر خارج عن سيطرتنا (مثل تأخر
                    شركات الشحن).
                </p>
            </div>
        ),
    },
    {
        id: 'force-majeure',
        title: 'القوة القاهرة',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    لا نتحمل المسؤولية عن التأخير أو الإخفاق الناتج عن ظروف خارجة
                    عن السيطرة المعقولة، مثل الكوارث الطبيعية، الانقطاعات العامة،
                    قيود الجهات الرسمية، أو تعطل مزودي الخدمات.
                </p>
            </div>
        ),
    },
    {
        id: 'privacy',
        title: 'الخصوصية',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    تخضع معالجة البيانات الشخصية لسياسة الخصوصية الخاصة بنا.
                    نوصيك بمراجعتها لفهم كيفية جمع البيانات واستخدامها وحقوقك.
                </p>
                <p>
                    <Link
                        className="font-semibold text-primary hover:underline"
                        href="/privacy"
                    >
                        الاطلاع على سياسة الخصوصية
                    </Link>
                    .
                </p>
            </div>
        ),
    },
    {
        id: 'changes',
        title: 'تعديل الشروط',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    قد نقوم بتحديث هذه الشروط من وقت لآخر. سننشر النسخة المحدثة
                    على هذه الصفحة مع تعديل تاريخ "آخر تحديث". استمرار استخدامك
                    للموقع بعد نشر التحديثات يعني قبولك لها.
                </p>
            </div>
        ),
    },
    {
        id: 'governing-law',
        title: 'القانون الواجب التطبيق وتسوية النزاعات',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>
                    تخضع هذه الشروط لـ {BRAND_DATA.governingLaw}. في حال نشوء
                    نزاع، سنسعى أولاً لحله ودياً عبر قنوات الدعم. وإذا تعذر ذلك،
                    تُحال المسألة للجهات المختصة وفق الأنظمة.
                </p>
            </div>
        ),
    },
    {
        id: 'contact',
        title: 'التواصل',
        body: (
            <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                <p>لأي استفسارات حول الشروط والأحكام، تواصل معنا:</p>
                <InfoBox>
                    <p className="text-sm font-bold text-foreground">
                        الدعم العام
                    </p>
                    <p className="mt-2">
                        <a
                            href={`mailto:${BRAND_DATA.email}`}
                            className="font-semibold text-primary hover:underline"
                        >
                            {BRAND_DATA.email}
                        </a>
                    </p>
                    <p className="mt-4 text-sm font-bold text-foreground">
                        الشؤون القانونية
                    </p>
                    <p className="mt-2">
                        <a
                            href={`mailto:${BRAND_DATA.legalEmail}`}
                            className="font-semibold text-primary hover:underline"
                        >
                            {BRAND_DATA.legalEmail}
                        </a>
                    </p>
                </InfoBox>
            </div>
        ),
    },
];

export default function TermsPage() {
    return (
        <StoreLayout>
            <Head title={`الشروط والأحكام | ${BRAND_DATA.nameAr}`}>
                <meta
                    name="description"
                    content="الشروط والأحكام الخاصة بزين ماركت: الاستخدام، الحسابات، الطلبات والدفع، الشحن، الإرجاع والاستبدال، الضمان، وحدود المسؤولية."
                />
            </Head>

            <HeroSection
                icon={FileText}
                nameAr={BRAND_DATA.nameAr}
                nameEn={BRAND_DATA.nameEn}
                title="الشروط والأحكام"
                description="هذه الشروط تنظّم استخدامك للموقع والخدمات، بما في ذلك الشراء والدفع والشحن والإرجاع والضمان."
                backgroundImage={ASSETS.heroBg}
                badges={
                    <>
                        <HeroBadge>
                            آخر تحديث: {formatDate(BRAND_DATA.lastUpdated)}
                        </HeroBadge>
                        <HeroBadge>المقر: {BRAND_DATA.location}</HeroBadge>
                        <HeroBadge href="/privacy">سياسة الخصوصية</HeroBadge>
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
                                    ملاحظة
                                </p>
                                <p className="mt-2 text-xs leading-5 text-muted-foreground">
                                    سياسات الشحن/الإرجاع/الضمان تفصّل بعض البنود
                                    المذكورة هنا، وتُعتبر جزءاً مكملاً لهذه الشروط.
                                </p>
                                <div className="mt-3 flex flex-wrap gap-2">
                                    <Link
                                        href="/shipping"
                                        className="rounded-full bg-background px-3 py-1 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted/50"
                                    >
                                        الشحن
                                    </Link>
                                    <Link
                                        href="/returns"
                                        className="rounded-full bg-background px-3 py-1 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted/50"
                                    >
                                        الإرجاع
                                    </Link>
                                    <Link
                                        href="/warranty"
                                        className="rounded-full bg-background px-3 py-1 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted/50"
                                    >
                                        الضمان
                                    </Link>
                                </div>
                            </InfoBox>
                        }
                    />
                }
            >
                <ContentCard>
                    {sections.map((s, idx) => (
                        <div key={s.id} id={s.id} className="scroll-mt-24">
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
                    title="تحتاج توضيحاً حول بند معيّن؟"
                    description="تواصل معنا وسنجيبك وفق السياسات المعتمدة."
                    actions={
                        <>
                            <Button asChild size="sm" variant="default">
                                <a href={`mailto:${BRAND_DATA.email}`}>
                                    مراسلة الدعم
                                </a>
                            </Button>
                            <Button asChild size="sm" variant="outline">
                                <Link href="/contact">صفحة التواصل</Link>
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
                title="تحتاج مساعدة الآن؟"
                description="فريق الدعم جاهز لمساعدتك في أي استفسارات حول هذه الشروط."
                brandName={BRAND_DATA.nameAr}
                primaryActions={
                    <Button asChild size="lg" variant="secondary">
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
                            href={`mailto:${BRAND_DATA.legalEmail}`}
                        >
                            {BRAND_DATA.legalEmail}
                        </ContactBadge>
                    </>
                }
            />
        </StoreLayout>
    );
}
