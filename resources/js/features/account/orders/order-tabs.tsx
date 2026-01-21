import { Badge } from '@/components/ui/badge';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { cn } from '@/lib/utils';

export type OrdersTabKey = 'active' | 'history' | 'returns';

export type OrdersTab = {
    key: OrdersTabKey;
    label: string;
    count: number;
};

export function OrdersTabs({
    tabs,
    activeTab,
    onTabChange,
}: {
    tabs: OrdersTab[];
    activeTab: OrdersTabKey;
    onTabChange: (tab: OrdersTabKey) => void;
}) {
    return (
        <Tabs
            value={activeTab}
            onValueChange={(value) => onTabChange(value as OrdersTabKey)}
        >
            <TabsList className="flex h-auto w-full flex-wrap gap-2 rounded-2xl bg-muted/60 p-2">
                {tabs.map((tab) => (
                    <TabsTrigger
                        key={tab.key}
                        value={tab.key}
                        className={cn(
                            'flex-1 justify-between rounded-xl px-4 py-2 text-sm',
                            'data-[state=active]:bg-background data-[state=active]:shadow-sm',
                        )}
                    >
                        <span>{tab.label}</span>
                        <Badge
                            variant="secondary"
                            className="rounded-full px-2 text-[11px]"
                        >
                            {tab.count}
                        </Badge>
                    </TabsTrigger>
                ))}
            </TabsList>
        </Tabs>
    );
}
