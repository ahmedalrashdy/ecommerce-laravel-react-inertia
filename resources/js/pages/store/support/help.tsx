import { Button } from '@/components/ui/button';
import {
    ContactBadge,
    FooterCTA,
    HeroBadge,
    HeroSection,
    InfoBox,
    PageLayout,
} from '@/features/support-and-legal/components';
import {
    ASSETS,
    BRAND_DATA,
} from '@/features/support-and-legal/constants/brand-data';
import StoreLayout from '@/layouts/StoreLayout';
import { Head, Link } from '@inertiajs/react';
import {
    CreditCard,
    HelpCircle,
    Mail,
    MessageCircle,
    Minus,
    Plus,
    RefreshCw,
    Search,
    Shield,
    ShoppingBag,
    Truck,
    User,
} from 'lucide-react';
import React, { useMemo, useState } from 'react';

const categories = [
    { key: 'orders', label: 'الطلبات', icon: ShoppingBag },
    { key: 'shipping', label: 'الشحن والتسليم', icon: Truck },
    { key: 'payments', label: 'الدفع', icon: CreditCard },
    { key: 'returns', label: 'الإرجاع والاستبدال', icon: RefreshCw },
    { key: 'warranty', label: 'الضمان', icon: Shield },
    { key: 'account', label: 'الحساب والخصوصية', icon: User },
];

const faqs = [
    // Orders
    {
        id: 'orders-1',
        category: 'orders',
        q: 'كيف أتابع حالة طلبي؟',
        a: 'بعد تسجيل الدخول، اذهب إلى صفحة الطلبات داخل حسابك لمشاهدة حالة الطلب وسجل التحديثات. كما ستصلك تحديثات حالة عبر البريد عند تغيّر الحالة.',
    },
    {
        id: 'orders-2',
        category: 'orders',
        q: 'هل يمكن تعديل الطلب بعد الدفع؟',
        a: 'يمكن ذلك قبل انتقال الطلب إلى مرحلة التجهيز للشحن. تواصل معنا مع رقم الطلب بأسرع وقت لنتمكن من التحقق من إمكانية التعديل.',
    },
    {
        id: 'orders-3',
        category: 'orders',
        q: 'ماذا أفعل إذا وصلني منتج مختلف عن المطلوب؟',
        a: 'قدّم طلب إرجاع/استبدال من صفحة الإرجاع، أو تواصل معنا مع رقم الطلب وصور للمنتج والملصق. سنراجع الحالة ونعالجها وفق السياسة.',
    },
    // Shipping
    {
        id: 'shipping-1',
        category: 'shipping',
        q: 'كم يستغرق تجهيز الطلب؟',
        a: 'نجهّز الطلبات عادةً خلال 24 ساعة عمل (خلال أيام العمل). قد تزيد المدة في مواسم الضغط أو للطلبات التي تحتاج تحققاً إضافياً.',
    },
    {
        id: 'shipping-2',
        category: 'shipping',
        q: 'كم يستغرق التوصيل؟',
        a: 'تختلف المدة حسب المدينة وشركة الشحن. ستظهر لك تقديرات التوصيل أثناء إتمام الطلب، كما يمكنك متابعة حالة الشحنة عبر رقم التتبع عند توفره.',
    },
    {
        id: 'shipping-3',
        category: 'shipping',
        q: 'هل يمكن تغيير عنوان الشحن بعد إنشاء الطلب؟',
        a: 'يمكن قبل بدء التجهيز للشحن. تواصل معنا فوراً مع رقم الطلب والعنوان الجديد، وسنؤكد لك إمكانية التعديل حسب مرحلة الطلب.',
    },
    // Payments
    {
        id: 'payments-1',
        category: 'payments',
        q: 'ما طرق الدفع المتاحة؟',
        a: 'تظهر طرق الدفع المتاحة أثناء إتمام الطلب (مثل بطاقات الدفع أو بوابات دفع أخرى). إذا واجهتك مشكلة، تواصل معنا لتقديم مساعدة فورية.',
    },
    {
        id: 'payments-2',
        category: 'payments',
        q: 'تم الخصم ولم يتم تأكيد الطلب، ماذا أفعل؟',
        a: 'أحياناً تتأخر عملية التأكيد بسبب الشبكة أو مزود الدفع. انتظر دقائق ثم راجع صفحة الطلبات. إذا استمرت المشكلة، أرسل لنا رقم العملية ووقت الدفع.',
    },
    {
        id: 'payments-3',
        category: 'payments',
        q: 'هل أستطيع الحصول على فاتورة؟',
        a: 'نعم، يمكنك طلب الفاتورة عبر التواصل معنا مع رقم الطلب، أو عبر صفحة الطلب (إن كانت ميزة الفاتورة مفعلة في حسابك).',
    },
    // Returns
    {
        id: 'returns-1',
        category: 'returns',
        q: 'كيف أقدّم طلب إرجاع أو استبدال؟',
        a: 'ادخل إلى صفحة الإرجاع/الاستبدال من حسابك، ثم اختر الطلب والعناصر وحدد السبب. ستصلك تحديثات بالحالة بعد مراجعة الطلب.',
    },
    {
        id: 'returns-2',
        category: 'returns',
        q: 'ما الحالات التي لا يقبل فيها الإرجاع؟',
        a: 'قد لا يُقبل الإرجاع في حالات مثل سوء الاستخدام أو تلف ناتج عن المستخدم أو غياب الملحقات الأساسية. تُراجع كل حالة بناءً على سياسة الإرجاع المحددة.',
    },
    {
        id: 'returns-3',
        category: 'returns',
        q: 'متى يتم رد المبلغ؟',
        a: 'يبدأ إجراء الاسترجاع بعد استلام المرتجع وفحصه. تختلف المدة حسب طريقة الاسترداد والبنك/مزود الدفع. سنشاركك تحديثات الحالة حتى الإكمال.',
    },
    // Warranty
    {
        id: 'warranty-1',
        category: 'warranty',
        q: 'كيف يعمل الضمان؟',
        a: 'الضمان يعتمد على نوع المنتج وسياسة الشركة المصنعة. احتفظ بالفاتورة وبيانات الطلب. عند وجود خلل مصنعي، تواصل معنا مع رقم الطلب لوصف المشكلة.',
    },
    {
        id: 'warranty-2',
        category: 'warranty',
        q: 'هل الضمان يغطي الكسر أو السوائل؟',
        a: 'في الغالب لا يغطي الضمان الأضرار الناتجة عن الكسر أو السوائل أو سوء الاستخدام. يتم تقييم كل حالة وفق شروط الضمان للمنتج.',
    },
    {
        id: 'warranty-3',
        category: 'warranty',
        q: 'كيف أرفع طلب ضمان؟',
        a: 'تواصل معنا عبر البريد أو واتساب مع رقم الطلب، وصور/فيديو مختصر يوضح المشكلة (إن أمكن). سنوجهك للخطوات التالية حسب نوع المنتج.',
    },
    // Account & Privacy
    {
        id: 'account-1',
        category: 'account',
        q: 'هل يجب إنشاء حساب لإتمام الطلب؟',
        a: 'يفضّل ذلك لتسهيل تتبع الطلبات والإرجاع، لكن قد يتوفر الشراء كضيف حسب إعدادات المتجر. إنشاء الحساب يمنحك تجربة متابعة أسرع.',
    },
    {
        id: 'account-2',
        category: 'account',
        q: 'نسيت كلمة المرور، ماذا أفعل؟',
        a: 'استخدم خيار "نسيت كلمة المرور" في صفحة تسجيل الدخول لإعادة التعيين عبر البريد. إذا واجهتك مشكلة، تواصل مع الدعم.',
    },
    {
        id: 'account-3',
        category: 'account',
        q: 'كيف يتم التعامل مع بياناتي؟',
        a: 'نستخدم بياناتك لتقديم الخدمة (الشحن، إشعارات الطلبات، الدعم). لا نشارك بياناتك مع أطراف غير لازمة لتقديم الخدمة. راجع سياسة الخصوصية إن كانت متاحة.',
    },
];

const quickLinks = [
    { title: 'الشحن والتسليم', href: '/shipping' },
    { title: 'الإرجاع والاستبدال', href: '/returns' },
    { title: 'الضمان', href: '/warranty' },
    { title: 'اتصل بنا', href: '/contact' },
];

function labelForCategory(key: string): string {
    const found = categories.find((c) => c.key === key);
    return found ? found.label : 'الكل';
}

function categoryLabel(key: string): string {
    switch (key) {
        case 'orders':
            return 'الطلبات';
        case 'shipping':
            return 'الشحن والتسليم';
        case 'payments':
            return 'الدفع';
        case 'returns':
            return 'الإرجاع والاستبدال';
        case 'warranty':
            return 'الضمان';
        case 'account':
            return 'الحساب والخصوصية';
        default:
            return 'عام';
    }
}

export default function HelpPage() {
    const [activeCategory, setActiveCategory] = useState('orders');
    const [query, setQuery] = useState('');
    const [openId, setOpenId] = useState<string | null>(null);

    const filteredFaqs = useMemo(() => {
        const q = query.trim().toLowerCase();
        return faqs
            .filter((f) =>
                activeCategory ? f.category === activeCategory : true,
            )
            .filter((f) => {
                if (!q) {
                    return true;
                }
                return (
                    f.q.toLowerCase().includes(q) ||
                    f.a.toLowerCase().includes(q)
                );
            });
    }, [activeCategory, query]);

    return (
        <StoreLayout>
            <Head title={`الأسئلة الشائعة | ${BRAND_DATA.nameAr}`}>
                <meta
                    name="description"
                    content="إجابات واضحة حول الشحن، الدفع، الإرجاع والاستبدال، الضمان، وتتبع الطلبات في زين ماركت."
                />
            </Head>

            <HeroSection
                icon={HelpCircle}
                nameAr={BRAND_DATA.nameAr}
                nameEn={BRAND_DATA.nameEn}
                title="مركز المساعدة"
                description="إجابات مختصرة وواضحة حول الطلبات، الشحن، الدفع، الإرجاع، والضمان."
                backgroundImage={ASSETS.heroBg}
                actions={
                    <>
                        <Button asChild size="lg" variant="secondary">
                            <Link href="/contact">اتصل بنا</Link>
                        </Button>
                        <Button
                            asChild
                            size="lg"
                            variant="outline"
                            className="bg-white/15 text-white ring-2 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25 hover:ring-white/40"
                        >
                            <a
                                href={`https://wa.me/${BRAND_DATA.whatsapp.replace('+', '')}`}
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                تواصل واتساب
                            </a>
                        </Button>
                    </>
                }
                badges={
                    <>
                        {quickLinks.map((l) => (
                            <HeroBadge key={l.title} href={l.href}>
                                {l.title}
                            </HeroBadge>
                        ))}
                    </>
                }
            />

            <PageLayout>
                <div className="grid gap-6 lg:grid-cols-12">
                    {/* Categories */}
                    <aside className="lg:col-span-4">
                        <div className="rounded-2xl border border-border bg-card p-5 shadow-sm">
                            <p className="text-sm font-bold text-foreground">
                                الأقسام
                            </p>

                            <div className="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-1">
                                {categories.map((c) => {
                                    const Icon = c.icon;
                                    const isActive = c.key === activeCategory;
                                    return (
                                        <button
                                            key={c.key}
                                            type="button"
                                            onClick={() => {
                                                setActiveCategory(c.key);
                                                setOpenId(null);
                                            }}
                                            className={`flex w-full items-center gap-3 rounded-xl border px-4 py-3 text-right text-sm transition-all focus:ring-2 focus:outline-none ${
                                                isActive
                                                    ? 'border-primary bg-primary text-primary-foreground shadow-md focus:ring-primary/50'
                                                    : 'border-border bg-background text-foreground hover:bg-muted/50 focus:ring-ring'
                                            }`}
                                        >
                                            <span
                                                className={`grid size-10 place-items-center rounded-lg transition-all ${
                                                    isActive
                                                        ? 'bg-primary-foreground/20'
                                                        : 'bg-muted/50'
                                                }`}
                                            >
                                                <Icon
                                                    className={`size-5 ${
                                                        isActive
                                                            ? 'text-primary-foreground'
                                                            : 'text-foreground'
                                                    }`}
                                                />
                                            </span>
                                            <span className="font-semibold">
                                                {c.label}
                                            </span>
                                        </button>
                                    );
                                })}
                            </div>

                            <InfoBox className="mt-5">
                                <p className="text-xs font-bold text-foreground">
                                    تواصل سريع
                                </p>
                                <p className="mt-2 text-xs leading-5 text-muted-foreground">
                                    ساعات العمل: {BRAND_DATA.hours}
                                </p>

                                <div className="mt-3 flex flex-wrap gap-2">
                                    <a
                                        href={`mailto:${BRAND_DATA.email}`}
                                        className="inline-flex items-center gap-2 rounded-full bg-background px-3 py-1.5 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted hover:ring-primary/30"
                                    >
                                        <Mail className="size-3.5" />
                                        بريد الدعم
                                    </a>
                                    <a
                                        href={`https://wa.me/${BRAND_DATA.whatsapp.replace('+', '')}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-2 rounded-full bg-background px-3 py-1.5 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted hover:ring-primary/30"
                                    >
                                        <MessageCircle className="size-3.5" />
                                        واتساب
                                    </a>
                                </div>
                            </InfoBox>
                        </div>
                    </aside>

                    {/* FAQ List */}
                    <div className="lg:col-span-8">
                        <div className="rounded-2xl border border-border bg-card p-5 shadow-sm">
                            <div className="mb-6">
                                <div className="relative">
                                    <span className="pointer-events-none absolute inset-y-0 right-3 grid place-items-center text-muted-foreground">
                                        <Search className="size-5" />
                                    </span>
                                    <input
                                        value={query}
                                        onChange={(e) =>
                                            setQuery(e.target.value)
                                        }
                                        placeholder="ابحث في الأسئلة الشائعة..."
                                        className="w-full rounded-xl border border-border bg-background px-10 py-3 text-sm text-foreground shadow-sm transition outline-none placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20"
                                    />
                                </div>
                                {query && (
                                    <p className="mt-2 text-xs text-muted-foreground">
                                        تلميح: ابحث بكلمات مثل "تتبع"، "إرجاع"،
                                        "ضمان".
                                    </p>
                                )}
                            </div>

                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 className="text-lg font-bold tracking-tight text-foreground sm:text-xl">
                                        الأسئلة الشائعة
                                    </h2>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        {labelForCategory(activeCategory)} •{' '}
                                        {filteredFaqs.length} نتيجة
                                    </p>
                                </div>

                                {query && (
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        onClick={() => {
                                            setQuery('');
                                            setOpenId(null);
                                        }}
                                    >
                                        مسح البحث
                                    </Button>
                                )}
                            </div>

                            <div className="mt-5 space-y-3">
                                {filteredFaqs.length === 0 ? (
                                    <InfoBox>
                                        <p className="text-sm font-bold text-foreground">
                                            لا توجد نتائج مطابقة
                                        </p>
                                        <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                            جرّب كلمات أبسط أو امسح البحث وأعد
                                            المحاولة.
                                        </p>
                                        <Button
                                            type="button"
                                            variant="default"
                                            size="sm"
                                            className="mt-4"
                                            onClick={() => {
                                                setQuery('');
                                                setOpenId(null);
                                            }}
                                        >
                                            مسح البحث
                                        </Button>
                                    </InfoBox>
                                ) : (
                                    filteredFaqs.map((f) => (
                                        <div
                                            key={f.id}
                                            className="rounded-xl border border-border bg-background"
                                        >
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    setOpenId(
                                                        openId === f.id
                                                            ? null
                                                            : f.id,
                                                    )
                                                }
                                                className="flex w-full items-start justify-between gap-4 rounded-xl px-4 py-4 text-right focus:ring-2 focus:ring-ring focus:outline-none"
                                                aria-expanded={openId === f.id}
                                            >
                                                <span className="min-w-0 flex-1">
                                                    <p className="text-sm font-bold text-foreground">
                                                        {f.q}
                                                    </p>
                                                    <p className="mt-1 text-xs text-muted-foreground">
                                                        {categoryLabel(
                                                            f.category,
                                                        )}
                                                    </p>
                                                </span>
                                                <span className="mt-1 grid size-8 shrink-0 place-items-center rounded-lg bg-muted/50 ring-1 ring-border">
                                                    {openId === f.id ? (
                                                        <Minus className="size-4 text-foreground" />
                                                    ) : (
                                                        <Plus className="size-4 text-foreground" />
                                                    )}
                                                </span>
                                            </button>

                                            {openId === f.id && (
                                                <div className="px-4 pb-4">
                                                    <InfoBox>
                                                        <p className="text-sm leading-7 text-muted-foreground">
                                                            {f.a}
                                                        </p>
                                                    </InfoBox>
                                                </div>
                                            )}
                                        </div>
                                    ))
                                )}
                            </div>

                            <InfoBox className="mt-6">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p className="text-sm font-bold text-foreground">
                                            لم تجد إجابتك؟
                                        </p>
                                        <p className="mt-1 text-sm text-muted-foreground">
                                            تواصل معنا وسنساعدك بشكل مباشر.
                                        </p>
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        <Button
                                            asChild
                                            size="sm"
                                            variant="default"
                                        >
                                            <Link href="/contact">
                                                اتصل بنا
                                            </Link>
                                        </Button>
                                        <Button
                                            asChild
                                            size="sm"
                                            variant="outline"
                                        >
                                            <a
                                                href={`https://wa.me/${BRAND_DATA.whatsapp.replace('+', '')}`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                واتساب
                                            </a>
                                        </Button>
                                    </div>
                                </div>
                            </InfoBox>
                        </div>

                        {/* Mini policies */}
                        <div className="mt-6 grid gap-4 sm:grid-cols-3">
                            <Link
                                href="/shipping"
                                className="group rounded-xl border border-border bg-card p-5 shadow-sm transition-all hover:-translate-y-1 hover:border-primary/30 hover:shadow-md focus:ring-2 focus:ring-ring focus:outline-none"
                            >
                                <div className="flex items-start gap-3">
                                    <span className="grid size-10 shrink-0 place-items-center rounded-lg bg-primary text-primary-foreground shadow-md transition-all group-hover:scale-110">
                                        <Truck className="size-5" />
                                    </span>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-bold text-foreground">
                                            الشحن والتسليم
                                        </p>
                                        <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                            مدد وتكاليف الشحن ومناطق الخدمة.
                                        </p>
                                        <p className="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all group-hover:gap-3">
                                            عرض التفاصيل{' '}
                                            <span aria-hidden="true">←</span>
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                href="/returns"
                                className="group rounded-xl border border-border bg-card p-5 shadow-sm transition-all hover:-translate-y-1 hover:border-primary/30 hover:shadow-md focus:ring-2 focus:ring-ring focus:outline-none"
                            >
                                <div className="flex items-start gap-3">
                                    <span className="grid size-10 shrink-0 place-items-center rounded-lg bg-primary text-primary-foreground shadow-md transition-all group-hover:scale-110">
                                        <RefreshCw className="size-5" />
                                    </span>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-bold text-foreground">
                                            الإرجاع والاستبدال
                                        </p>
                                        <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                            الشروط والخطوات وحالة الطلب.
                                        </p>
                                        <p className="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all group-hover:gap-3">
                                            عرض التفاصيل{' '}
                                            <span aria-hidden="true">←</span>
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link
                                href="/warranty"
                                className="group rounded-xl border border-border bg-card p-5 shadow-sm transition-all hover:-translate-y-1 hover:border-primary/30 hover:shadow-md focus:ring-2 focus:ring-ring focus:outline-none"
                            >
                                <div className="flex items-start gap-3">
                                    <span className="grid size-10 shrink-0 place-items-center rounded-lg bg-primary text-primary-foreground shadow-md transition-all group-hover:scale-110">
                                        <Shield className="size-5" />
                                    </span>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-sm font-bold text-foreground">
                                            الضمان
                                        </p>
                                        <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                            ماذا يغطي الضمان وكيفية التقديم.
                                        </p>
                                        <p className="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all group-hover:gap-3">
                                            عرض التفاصيل{' '}
                                            <span aria-hidden="true">←</span>
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </PageLayout>

            <FooterCTA
                title="دعم واضح، بدون تعقيد"
                description="إذا كانت لديك تفاصيل مرتبطة بطلبك، جهّز رقم الطلب عند التواصل لتسريع المعالجة."
                brandName={BRAND_DATA.nameAr}
                primaryActions={
                    <Button asChild size="lg" variant="secondary">
                        <Link href="/products">تصفح المنتجات</Link>
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
                            icon={MessageCircle}
                            href={`https://wa.me/${BRAND_DATA.whatsapp.replace('+', '')}`}
                            external
                        >
                            {BRAND_DATA.whatsappDisplay}
                        </ContactBadge>
                    </>
                }
            />
        </StoreLayout>
    );
}
