import { Head, Link, usePage } from '@inertiajs/react';
import { BarChart, Bar, LineChart, Line, PieChart, Pie, Cell, XAxis, YAxis, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import StatCard from '@/Components/StatCard';
import DataTable from '@/Components/DataTable';

const COLORS = ['#2563eb', '#0d9488', '#ea580c', '#7c3aed', '#16a34a', '#dc2626'];

export default function Dashboard({ cards, widgets, charts, assigneeAssignments }) {
    const { auth } = usePage().props;

    if (auth.user.role === 'assignee') {
        return <AssigneeDashboard assignments={assigneeAssignments || []} />;
    }

    return (
        <AuthenticatedLayout header="Dashboard">
            <Head title="Dashboard" />

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <StatCard label="Total Assets" value={cards.totalAssets} />
                <StatCard label="Available Stock" value={cards.availableStock} accent="green" />
                <StatCard label="Assigned Items" value={cards.assignedItems} accent="orange" />
                <StatCard label="This Month Cost" value={Number(cards.thisMonthCost).toLocaleString()} accent="purple" />
                <StatCard label="Total Asset Value" value={Number(cards.totalAssetValue).toLocaleString()} />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <ChartCard title="Monthly Purchase Cost">
                    <ResponsiveContainer width="100%" height={260}>
                        <LineChart data={charts.monthlyPurchases}>
                            <XAxis dataKey="month" tick={{ fontSize: 11 }} />
                            <YAxis tick={{ fontSize: 11 }} />
                            <Tooltip />
                            <Line type="monotone" dataKey="total" stroke="#2563eb" strokeWidth={2} />
                        </LineChart>
                    </ResponsiveContainer>
                </ChartCard>

                <ChartCard title="Category Distribution">
                    <ResponsiveContainer width="100%" height={260}>
                        <PieChart>
                            <Pie
                                data={charts.categoryDistribution}
                                dataKey="total"
                                nameKey="category.name"
                                outerRadius={90}
                                label={(d) => d.category?.name}
                            >
                                {charts.categoryDistribution.map((_, i) => (
                                    <Cell key={i} fill={COLORS[i % COLORS.length]} />
                                ))}
                            </Pie>
                            <Tooltip />
                        </PieChart>
                    </ResponsiveContainer>
                </ChartCard>

                <ChartCard title="Available vs Assigned">
                    <ResponsiveContainer width="100%" height={220}>
                        <BarChart
                            data={[
                                { name: 'Available', value: charts.availableVsAssigned.available },
                                { name: 'Assigned', value: charts.availableVsAssigned.assigned },
                            ]}
                        >
                            <XAxis dataKey="name" tick={{ fontSize: 11 }} />
                            <YAxis tick={{ fontSize: 11 }} />
                            <Tooltip />
                            <Bar dataKey="value" fill="#0d9488" radius={[6, 6, 0, 0]} />
                        </BarChart>
                    </ResponsiveContainer>
                </ChartCard>

                <ChartCard title="Low Stock">
                    <DataTable
                        columns={[
                            { key: 'item_name', label: 'Item' },
                            { key: 'available_quantity', label: 'Available' },
                        ]}
                        rows={widgets.lowStock}
                        emptyMessage="No low-stock consumables."
                    />
                </ChartCard>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <Widget title="Recent Assignments">
                    {widgets.recentAssignments.map((a) => (
                        <WidgetRow key={a.id} left={a.inventory?.item_name} right={a.assignee?.full_name} />
                    ))}
                </Widget>
                <Widget title="Pending Returns">
                    {widgets.pendingReturns.map((a) => (
                        <WidgetRow key={a.id} left={a.inventory?.item_name} right={`${a.assignee?.full_name} (${a.status})`} />
                    ))}
                </Widget>
                <Widget title="Warranty Expiring">
                    {widgets.warrantyExpiring.map((i) => (
                        <WidgetRow key={i.id} left={i.item_name} right={i.warranty_end} />
                    ))}
                </Widget>
            </div>
        </AuthenticatedLayout>
    );
}

function AssigneeDashboard({ assignments }) {
    return (
        <AuthenticatedLayout header="My Assigned Items">
            <Head title="Dashboard" />
            <DataTable
                columns={[
                    { key: 'asset_code', label: 'Asset Code', render: (r) => r.inventory?.asset_code },
                    { key: 'item_name', label: 'Item', render: (r) => r.inventory?.item_name },
                    { key: 'category', label: 'Category', render: (r) => r.inventory?.category?.name },
                    { key: 'quantity', label: 'Qty' },
                    { key: 'issue_date', label: 'Issue Date' },
                    { key: 'status', label: 'Status' },
                ]}
                rows={assignments}
                emptyMessage="You have no items assigned to you."
            />
        </AuthenticatedLayout>
    );
}

function ChartCard({ title, children }) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 className="text-sm font-semibold text-gray-900 mb-3">{title}</h3>
            {children}
        </div>
    );
}

function Widget({ title, children }) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 className="text-sm font-semibold text-gray-900 mb-3">{title}</h3>
            <div className="space-y-2">
                {children}
                {(!children || children.length === 0) && <p className="text-sm text-gray-400">Nothing to show.</p>}
            </div>
        </div>
    );
}

function WidgetRow({ left, right }) {
    return (
        <div className="flex justify-between text-sm border-b border-gray-100 pb-2">
            <span className="text-gray-700">{left}</span>
            <span className="text-gray-400">{right}</span>
        </div>
    );
}
