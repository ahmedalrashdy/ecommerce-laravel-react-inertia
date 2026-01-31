import { Button } from '@/components/ui/button';
import StoreLayout from '@/layouts/StoreLayout';
import { Head, Link } from '@inertiajs/react';
import {
    CheckCircle2,
    Mail,
    MapPin,
    MessageCircle,
    RefreshCw,
    Shield,
    Star,
    Truck,
    Zap,
} from 'lucide-react';

// Static data arrays
const brand = {
    nameAr: 'زين ماركت',
    nameEn: 'Zain Market',
    tagline: 'نُسهّل عليك اختيار الإلكترونيات بثقة',
    description:
        'في زين ماركت نختار منتجاتنا بعناية، ونوثّق مواصفاتها، ونجهّز الطلبات بسرعة—لتصل إليك تجربة شراء واضحة من أول نقرة وحتى ما بعد الاستلام.',
    founded: '2019',
    location: 'الرياض – المملكة العربية السعودية',
    hours: 'السبت–الخميس 10:00–22:00',
    email: 'support@zain.market',
    whatsapp: '+966 55 123 9876',
};

const assets = {
    heroBg: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=2400&q=80',
    warehouse:
        'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=2000&q=80',
    support:
        'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=2000&q=80',
    team: [
        'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1550525811-e5869dd03032?auto=format&fit=crop&w=1200&q=80',
    ],
};

const quickBadges = [
    'منتجات أصلية',
    'تجهيز خلال 24 ساعة عمل',
    'دعم ما بعد البيع',
];

const timeline = [
    {
        year: '2019',
        title: 'إطلاق المتجر ونواة الكتالوج',
        body: 'بدأنا بفئات أساسية من الإلكترونيات والملحقات، مع تركيز صارم على دقة المواصفات والصور.',
    },
    {
        year: '2021',
        title: 'توسعة الشحن وتحسين تجربة الدفع',
        body: 'رفعنا كفاءة التجهيز، وأعدنا تصميم رحلة الشراء لتكون أسرع وأوضح على الجوال.',
    },
    {
        year: '2023',
        title: 'إدارة مرتجعات منظمة وتتبع المخزون',
        body: 'اعتمدنا تدفق عمل واضح للإرجاع/الاستبدال وسجلّات حركة المخزون لضمان الدقة.',
    },
    {
        year: '2025',
        title: 'تحسين المتغيرات والمراجعات',
        body: 'طوّرنا عرض المتغيرات (ألوان/سعات) ورفعنا جودة المراجعات لضمان قرار شراء أدق.',
    },
];

const values = [
    {
        title: 'الوضوح',
        desc: 'مواصفات دقيقة وصور واضحة وسياسات مكتوبة بدون غموض.',
        icon: CheckCircle2,
    },
    {
        title: 'الجودة',
        desc: 'منتجات أصلية وفحص قبل الشحن لضمان مطابقة المتغير والسعر.',
        icon: Star,
    },
    {
        title: 'السرعة',
        desc: 'نجهّز الطلبات عادةً خلال 24 ساعة عمل مع تحديثات حالة متتابعة.',
        icon: Zap,
    },
    {
        title: 'الدعم',
        desc: 'فريق خدمة عملاء متعدد القنوات قبل وبعد الشراء.',
        icon: MessageCircle,
    },
    {
        title: 'الثقة',
        desc: 'سياسات إرجاع واستبدال وضمان واضحة لتجربة مطمئنة.',
        icon: Shield,
    },
];

const stats = [
    { label: 'عميل', value: '+120K' },
    { label: 'طلب مُسلّم', value: '+35K' },
    { label: 'متوسط التقييم', value: '4.7/5' },
    { label: 'متوسط تجهيز', value: '24 ساعة' },
];

const howWeWork = [
    {
        title: 'فحص قبل الإرسال',
        body: 'مطابقة SKU والمتغير (مثل اللون/السعة) وفحص سلامة المحتوى قبل التغليف.',
    },
    {
        title: 'تغليف يحمي المنتج',
        body: 'مواد تغليف مناسبة لطبيعة المنتج، مع مراعاة النقل والاهتزاز.',
    },
    {
        title: 'تحديثات حالة الطلب',
        body: 'سجل واضح لحالة الطلب من التجهيز حتى التسليم لتحسين الشفافية.',
    },
    {
        title: 'تدفق مرتجعات منظم',
        body: 'تتبع سبب الإرجاع وحالة الفحص والقرار المالي بطريقة قابلة للتدقيق.',
    },
];

const team = [
    {
        name: 'هند القحطاني',
        role: 'إدارة العمليات',
        quote: 'تؤمن أن تفاصيل التجهيز الصغيرة تصنع فرق الثقة الكبير.',
        img: assets.team[0],
    },
    {
        name: 'سلمان العتيبي',
        role: 'تجربة المنتج',
        quote: 'يركّز على جعل قرار الشراء أسهل عبر معلومات دقيقة ومقارنة واضحة.',
        img: assets.team[1],
    },
    {
        name: 'ريم الشهري',
        role: 'خدمة العملاء',
        quote: 'تهتم بأن يشعر العميل أنه مسموع ومفهوم في كل تواصل.',
        img: assets.team[2],
    },
    {
        name: 'ناصر الغامدي',
        role: 'الشحن والتجهيز',
        quote: 'يؤمن أن السرعة لا تُغني عن الدقة—بل تتطلبها.',
        img: assets.team[3],
    },
];

// TODO: Create static pages for /shipping, /returns, /warranty, and /contact
const policies = [
    {
        title: 'الشحن والتسليم',
        desc: 'تفاصيل مناطق الشحن، مدة التوصيل، وتكاليف الخدمة.',
        href: '/shipping',
        icon: Truck,
    },
    {
        title: 'الإرجاع والاستبدال',
        desc: 'آلية تقديم طلب إرجاع، المعايير، والمدة الزمنية.',
        href: '/returns',
        icon: RefreshCw,
    },
    {
        title: 'الضمان',
        desc: 'سياسة الضمان حسب نوع المنتج وآلية الاستفادة منه.',
        href: '/warranty',
        icon: Shield,
    },
];

export default function AboutPage() {
    return (
        <StoreLayout>
            <Head title={`من نحن | ${brand.nameAr}`}>
                <meta
                    name="description"
                    content="تعرف على زين ماركت: قصتنا، قيمنا، فريقنا، وكيف نضمن لك تجربة شراء وشحن موثوقة."
                />
            </Head>

            {/* HERO */}
            <section className="relative overflow-hidden">
                <div className="absolute inset-0 z-0">
                    <img
                        src={assets.heroBg}
                        alt="خلفية متجر زين ماركت"
                        className="h-full w-full object-cover"
                        loading="eager"
                        fetchPriority='high'
                        decoding='async'
                    />
                    <div className="absolute inset-0 bg-linear-to-b from-black/70 via-black/50 to-transparent" />
                </div>

                <div className="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="py-16 sm:py-24 lg:py-32">
                        <div className="max-w-2xl">
                            {/* Brand Mark */}
                            <div className="inline-flex items-center gap-3">
                                <div className="grid size-12 place-items-center rounded-2xl bg-white/15 ring-2 ring-white/30 backdrop-blur-md transition-all hover:scale-110 hover:bg-white/20">
                                    <Star className="size-6 text-white" />
                                </div>
                                <div className="leading-tight">
                                    <p className="text-sm font-bold text-white">
                                        {brand.nameAr}
                                    </p>
                                    <p className="text-xs font-medium text-white/90">
                                        {brand.nameEn}
                                    </p>
                                </div>
                            </div>

                            <h1 className="mt-8 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                                {brand.tagline}
                            </h1>

                            <p className="mt-6 text-lg leading-8 text-white/95 sm:text-xl">
                                {brand.description}
                            </p>

                            <div className="mt-8 flex flex-wrap gap-4">
                                <Button
                                    asChild
                                    size="lg"
                                    variant="secondary"
                                    className="text-base font-semibold shadow-lg transition-all hover:scale-105 hover:shadow-xl"
                                >
                                    <Link href="/products">تصفح المنتجات</Link>
                                </Button>
                                <Button
                                    asChild
                                    size="lg"
                                    variant="outline"
                                    className="bg-white/15 text-white ring-2 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25 hover:ring-white/40"
                                >
                                    <Link href="/contact">تواصل معنا</Link>
                                </Button>
                            </div>

                            <div className="mt-10 flex flex-wrap gap-3">
                                {quickBadges.map((badge) => (
                                    <span
                                        key={badge}
                                        className="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25"
                                    >
                                        {badge}
                                    </span>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* TRUST STRIP */}
            <section className="relative z-10 container mx-auto -mt-8 px-4 sm:px-6 lg:px-8">
                <div className="grid gap-4 rounded-2xl border border-border bg-card p-6 shadow-lg backdrop-blur-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div className="rounded-xl bg-muted/60 p-5 transition-all hover:-translate-y-1 hover:bg-muted/80 hover:shadow-md">
                        <p className="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                            سنة التأسيس
                        </p>
                        <p className="mt-2 text-lg font-bold text-foreground">
                            {brand.founded}
                        </p>
                    </div>
                    <div className="rounded-xl bg-muted/60 p-5 transition-all hover:-translate-y-1 hover:bg-muted/80 hover:shadow-md">
                        <p className="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                            المقر
                        </p>
                        <p className="mt-2 text-base leading-tight font-bold text-foreground">
                            {brand.location}
                        </p>
                    </div>
                    <div className="rounded-xl bg-muted/60 p-5 transition-all hover:-translate-y-1 hover:bg-muted/80 hover:shadow-md">
                        <p className="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                            ساعات العمل
                        </p>
                        <p className="mt-2 text-base font-bold text-foreground">
                            {brand.hours}
                        </p>
                    </div>
                    <div className="rounded-xl bg-muted/60 p-5 transition-all hover:-translate-y-1 hover:bg-muted/80 hover:shadow-md">
                        <p className="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                            الدعم
                        </p>
                        <p className="mt-2 text-sm font-bold break-all text-foreground">
                            {brand.email}
                        </p>
                    </div>
                </div>
            </section>

            {/* STORY + TIMELINE */}
            <section className="container mx-auto px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                <div className="grid gap-10 lg:grid-cols-12 lg:items-start">
                    <div className="lg:col-span-5">
                        <h2 className="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">
                            قصتنا
                        </h2>
                        <p className="mt-4 text-base leading-7 text-muted-foreground">
                            بدأت {brand.nameAr} في {brand.founded} كفكرة بسيطة:
                            متجر إلكتروني يقدّم تفاصيل حقيقية عن المنتجات بدل
                            النصوص العامة، مع شحن سريع وسياسات واضحة. اليوم نخدم
                            آلاف العملاء داخل المملكة، ونطوّر تجربة المتجر
                            باستمرار عبر تحسين البحث، المتغيرات
                            (الألوان/السعات)، ومراجعات العملاء.
                        </p>

                        <div className="mt-6 rounded-2xl border border-border bg-muted/50 p-5">
                            <div className="flex items-start gap-3">
                                <div className="mt-1">
                                    <MapPin className="size-5 text-primary" />
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-foreground">
                                        أين نعمل؟
                                    </p>
                                    <p className="mt-1 text-sm leading-6 text-muted-foreground">
                                        مركز عملياتنا في {brand.location}. تجهيز
                                        الطلبات يتم عادةً خلال 24 ساعة عمل، مع
                                        متابعة واضحة لحالة الطلب.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="lg:col-span-7">
                        <div className="rounded-2xl border border-border bg-card p-6">
                            <h3 className="text-base font-semibold text-foreground">
                                محطات رئيسية
                            </h3>

                            <ol className="mt-5 space-y-5">
                                {timeline.map((t) => (
                                    <li
                                        key={t.year}
                                        className="flex gap-4"
                                    >
                                        <div className="flex flex-col items-center">
                                            <span className="grid size-9 place-items-center rounded-full border border-border bg-muted/50 text-sm font-semibold text-foreground">
                                                {t.year}
                                            </span>
                                            <span className="mt-2 h-full w-px bg-border" />
                                        </div>

                                        <div className="pb-2">
                                            <p className="text-sm font-semibold text-foreground">
                                                {t.title}
                                            </p>
                                            <p className="mt-1 text-sm leading-6 text-muted-foreground">
                                                {t.body}
                                            </p>
                                        </div>
                                    </li>
                                ))}
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            {/* VALUES */}
            <section className="bg-muted/50">
                <div className="container mx-auto px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                    <div className="max-w-2xl">
                        <h2 className="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">
                            ما الذي نؤمن به؟
                        </h2>
                        <p className="mt-3 text-base leading-7 text-muted-foreground">
                            نُفضّل بناء الثقة عبر تفاصيل صغيرة: مواصفات دقيقة،
                            سياسات واضحة، وخدمة ما بعد البيع.
                        </p>
                    </div>

                    <div className="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                        {values.map((v, index) => {
                            const Icon = v.icon;
                            return (
                                <div
                                    key={v.title}
                                    className="group rounded-2xl border border-border bg-card p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-lg"
                                    style={{
                                        animationDelay: `${index * 100}ms`,
                                    }}
                                >
                                    <div className="mb-4 flex items-center gap-3">
                                        <span className="grid size-12 place-items-center rounded-xl bg-primary text-primary-foreground shadow-md transition-all group-hover:scale-110 group-hover:shadow-lg">
                                            <Icon className="size-6" />
                                        </span>
                                        <p className="text-base font-bold text-foreground">
                                            {v.title}
                                        </p>
                                    </div>
                                    <p className="text-sm leading-6 text-muted-foreground">
                                        {v.desc}
                                    </p>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* STATS */}
            <section className="container mx-auto px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                <div className="grid gap-5 rounded-2xl border border-border bg-card p-8 shadow-lg sm:grid-cols-2 lg:grid-cols-4">
                    {stats.map((s, index) => (
                        <div
                            key={s.label}
                            className="group rounded-xl bg-muted/60 p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:bg-muted/80 hover:shadow-md"
                            style={{
                                animationDelay: `${index * 100}ms`,
                            }}
                        >
                            <p className="text-4xl font-bold tracking-tight text-primary transition-all group-hover:scale-110 sm:text-5xl">
                                {s.value}
                            </p>
                            <p className="mt-3 text-sm font-semibold tracking-wide text-muted-foreground uppercase">
                                {s.label}
                            </p>
                        </div>
                    ))}
                </div>
            </section>

            {/* HOW WE WORK */}
            <section className="bg-background">
                <div className="container mx-auto px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                    <div className="grid gap-10 lg:grid-cols-12 lg:items-center">
                        <div className="lg:col-span-5">
                            <h2 className="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">
                                كيف نشتغل؟
                            </h2>
                            <p className="mt-3 text-base leading-7 text-muted-foreground">
                                نهتم بالمنتج قبل أن يصل لك، ونُسجّل كل خطوة بشكل
                                يساعد على الشفافية وحل أي إشكال بسرعة.
                            </p>

                            <div className="mt-8 space-y-4">
                                {howWeWork.map((w, index) => (
                                    <div
                                        key={w.title}
                                        className="group rounded-2xl border border-border bg-muted/50 p-6 transition-all duration-300 hover:-translate-y-0.5 hover:border-primary/20 hover:bg-muted/70 hover:shadow-md"
                                        style={{
                                            animationDelay: `${index * 100}ms`,
                                        }}
                                    >
                                        <div className="flex items-start gap-4">
                                            <div className="flex size-8 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-sm font-bold text-primary transition-all group-hover:scale-110 group-hover:bg-primary/20">
                                                {index + 1}
                                            </div>
                                            <div className="flex-1">
                                                <p className="text-base font-bold text-foreground">
                                                    {w.title}
                                                </p>
                                                <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                                    {w.body}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="lg:col-span-7">
                            <div className="overflow-hidden rounded-2xl border border-border bg-muted/50 shadow-sm">
                                <img
                                    src={assets.warehouse}
                                    alt="مستودع وتجهيز الطلبات"
                                    className="h-[360px] w-full object-cover sm:h-[440px]"
                                    loading="lazy"
                    decoding="async"
                                />
                            </div>

                            <div className="mt-4 overflow-hidden rounded-2xl border border-border bg-muted/50 shadow-sm">
                                <img
                                    src={assets.support}
                                    alt="فريق خدمة العملاء"
                                    className="h-[240px] w-full object-cover sm:h-[260px]"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* TEAM */}
            <section className="bg-muted/50">
                <div className="container mx-auto px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                    <div className="max-w-2xl">
                        <h2 className="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">
                            فريقنا
                        </h2>
                        <p className="mt-3 text-base leading-7 text-muted-foreground">
                            فريق صغير بتركيز كبير: تشغيل، تجربة، دعم، وتجهيز.
                        </p>
                    </div>

                    <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {team.map((m, index) => (
                            <div
                                key={m.name}
                                className="group overflow-hidden rounded-2xl border border-border bg-card shadow-sm transition-all duration-300 hover:-translate-y-2 hover:border-primary/30 hover:shadow-xl"
                                style={{
                                    animationDelay: `${index * 100}ms`,
                                }}
                            >
                                <div className="relative overflow-hidden">
                                    <img
                                        src={m.img}
                                        alt={`صورة ${m.name}`}
                                        className="h-56 w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                    <div className="absolute inset-0 bg-linear-to-t from-black/40 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />
                                </div>
                                <div className="p-6">
                                    <p className="text-base font-bold text-foreground">
                                        {m.name}
                                    </p>
                                    <p className="mt-1 text-sm font-medium text-primary">
                                        {m.role}
                                    </p>
                                    <p className="mt-4 text-sm leading-6 text-muted-foreground italic">
                                        "{m.quote}"
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* POLICIES */}
            <section className="bg-background">
                <div className="container mx-auto px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                    <div className="max-w-2xl">
                        <h2 className="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">
                            سياسات واضحة
                        </h2>
                        <p className="mt-3 text-base leading-7 text-muted-foreground">
                            قبل الشراء وبعده، نفضّل أن تكون الأمور مكتوبة بشكل
                            واضح ومتاح.
                        </p>
                    </div>

                    <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {policies.map((p) => {
                            const Icon = p.icon;
                            return (
                                <Link
                                    key={p.title}
                                    href={p.href}
                                    className="group rounded-2xl border border-border bg-card p-6 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:border-primary/40 hover:shadow-xl focus:ring-2 focus:ring-ring focus:outline-none"
                                >
                                    <div className="flex items-start gap-4">
                                        <span className="grid size-12 shrink-0 place-items-center rounded-xl bg-primary text-primary-foreground shadow-md transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg">
                                            <Icon className="size-6" />
                                        </span>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-base font-bold text-foreground">
                                                {p.title}
                                            </p>
                                            <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                                {p.desc}
                                            </p>
                                            <p className="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all group-hover:gap-3">
                                                عرض التفاصيل
                                                <span className="transition-transform duration-300 group-hover:translate-x-[-4px]">
                                                    ←
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* FINAL CTA */}
            <section className="bg-primary py-16 sm:py-20">
                <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-4xl">
                        <div className="rounded-2xl border border-primary-foreground/10 bg-primary-foreground/5 p-8 sm:p-10 lg:p-12">
                            <div className="mb-8 text-center">
                                <h2 className="text-3xl font-bold text-primary-foreground sm:text-4xl">
                                    جاهز للتجربة؟
                                </h2>
                                <p className="mt-4 text-lg text-primary-foreground/90">
                                    ابدأ التسوق الآن، وإن احتجت شيئاً—الدعم متاح
                                    عبر البريد أو واتساب.
                                </p>
                            </div>

                            <div className="mb-8 flex flex-col items-center gap-6 sm:flex-row sm:justify-center">
                                <Button
                                    asChild
                                    size="lg"
                                    variant="secondary"
                                    className="w-full px-8 text-base font-semibold sm:w-auto"
                                >
                                    <Link href="/products">ابدأ التسوق</Link>
                                </Button>
                            </div>

                            <div className="flex flex-col items-center gap-4 border-t border-primary-foreground/10 pt-6 sm:flex-row sm:justify-center">
                                <a
                                    href={`mailto:${brand.email}`}
                                    className="inline-flex items-center gap-2 rounded-full bg-primary-foreground/10 px-5 py-2.5 text-sm font-medium text-primary-foreground transition-all hover:bg-primary-foreground/20"
                                >
                                    <Mail className="size-4" />
                                    <span>{brand.email}</span>
                                </a>
                                <a
                                    href={`https://wa.me/${brand.whatsapp.replace(/\s/g, '')}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="inline-flex items-center gap-2 rounded-full bg-primary-foreground/10 px-5 py-2.5 text-sm font-medium text-primary-foreground transition-all hover:bg-primary-foreground/20"
                                >
                                    <MessageCircle className="size-4" />
                                    <span>{brand.whatsapp}</span>
                                </a>
                            </div>
                        </div>

                        <p className="mt-8 text-center text-sm text-primary-foreground/70">
                            © {new Date().getFullYear()} {brand.nameAr}. جميع
                            الحقوق محفوظة.
                        </p>
                    </div>
                </div>
            </section>
        </StoreLayout>
    );
}
