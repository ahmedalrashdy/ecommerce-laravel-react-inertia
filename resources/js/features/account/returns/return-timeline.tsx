import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Clock } from 'lucide-react';

export function ReturnTimeline({
    timeline,
}: {
    timeline: App.Data.Basic.ReturnTimelineData[];
}) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-base">سجل الحالة</CardTitle>
            </CardHeader>
            <CardContent>
                {timeline.length === 0 ? (
                    <div className="rounded-lg border border-dashed bg-muted/20 p-6 text-center text-sm text-muted-foreground">
                        لا توجد تحديثات حتى الآن.
                    </div>
                ) : (
                    <div className="space-y-4">
                        {timeline.map((entry, index) => (
                            <div
                                key={`${entry.status}-${index}`}
                                className="flex gap-3"
                            >
                                <div className="flex flex-col items-center">
                                    <span className="flex h-8 w-8 items-center justify-center rounded-full border bg-background text-xs font-semibold">
                                        {index + 1}
                                    </span>
                                    {index < timeline.length - 1 && (
                                        <span className="h-full w-px bg-border" />
                                    )}
                                </div>
                                <div className="flex-1 space-y-1">
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <span className="font-semibold text-foreground">
                                            {entry.statusLabel}
                                        </span>
                                        <span className="flex items-center gap-1 text-xs text-muted-foreground">
                                            <Clock className="h-3 w-3" />
                                            {entry.createdAt}
                                        </span>
                                    </div>
                                    {entry.comment && (
                                        <p className={cn('text-sm text-muted-foreground')}>
                                            {entry.comment}
                                        </p>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
