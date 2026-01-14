import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useNewsletterSubscription } from '@/hooks/use-newsletter-subscription';
import { CheckCircle2, Mail, Send } from 'lucide-react';
import * as React from 'react';
import styles from './Newsletter.module.css';

export const Newsletter: React.FC = () => {
    const {
        email,
        setEmail,
        handleSubmit,
        isLoading,
        isSuccess: isSubmitted,
        successMessage,
        errorMessage,
    } = useNewsletterSubscription();

    return (
        <section className={styles.section}>
            {/* Background Pattern */}
            <div className={styles.backgroundPattern}>
                <div className={styles.patternCircle1} />
                <div className={styles.patternCircle2} />
            </div>

            <div className={styles.container}>
                <div className={styles.content}>
                    {/* Icon */}
                    <div className={styles.iconContainer}>
                        <Mail className={styles.icon} />
                    </div>

                    {/* Heading */}
                    <h2 className={styles.heading}>
                        اشترك في نشرتنا الإخبارية
                    </h2>
                    <p className={styles.subheading}>
                        احصل على آخر العروض والخصومات الحصرية مباشرة إلى بريدك
                        الإلكتروني
                    </p>

                    {/* Newsletter Form */}
                    {!isSubmitted ? (
                        <form
                            onSubmit={handleSubmit}
                            className={styles.form}
                        >
                            <div className={styles.inputWrapper}>
                                <Input
                                    type="email"
                                    placeholder="أدخل بريدك الإلكتروني"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    required
                                    className={styles.input}
                                    disabled={isLoading}
                                />
                                {errorMessage && (
                                    <p className="mt-2 text-sm text-red-600">
                                        {errorMessage}
                                    </p>
                                )}
                            </div>
                            <Button
                                type="submit"
                                size="lg"
                                disabled={isLoading}
                                className={styles.submitButton}
                            >
                                {isLoading ? (
                                    'جاري الإرسال...'
                                ) : (
                                    <>
                                        اشترك الآن
                                        <Send
                                            className={styles.submitButtonIcon}
                                        />
                                    </>
                                )}
                            </Button>
                        </form>
                    ) : (
                        <div className={styles.successContainer}>
                            <div className={styles.successContent}>
                                <CheckCircle2 className={styles.successIcon} />
                                <p className={styles.successMessage}>
                                    {successMessage ||
                                        'شكراً لك! تم الاشتراك بنجاح'}
                                </p>
                            </div>
                        </div>
                    )}

                    {/* Features */}
                    <div className={styles.features}>
                        <div className={styles.feature}>
                            <CheckCircle2 className={styles.featureIcon} />
                            <span>عروض حصرية</span>
                        </div>
                        <div className={styles.feature}>
                            <CheckCircle2 className={styles.featureIcon} />
                            <span>أحدث المنتجات</span>
                        </div>
                        <div className={styles.feature}>
                            <CheckCircle2 className={styles.featureIcon} />
                            <span>نصائح وإرشادات</span>
                        </div>
                    </div>

                    {/* Privacy Note */}
                    <p className={styles.privacyNote}>
                        نحن نحترم خصوصيتك. يمكنك إلغاء الاشتراك في أي وقت.
                    </p>
                </div>
            </div>
        </section>
    );
};
