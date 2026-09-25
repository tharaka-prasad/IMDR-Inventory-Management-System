import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import SearchFilter from '@/Components/SearchFilter';
import Pagination from '@/Components/Pagination';

export default function Index({ records, filters }) {
    return (
        <AuthenticatedLayout header="Maintenance">
            <Head title="Maintenance" />

            <div className="flex justify-end mb-4">
                <Link href={route('maintenance.create')} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700">
                    + Add Repair
                </Link>
            </div>

            <SearchFilter
                routeName={route('maintenance.index')}
                initial={filters}
                fields={[
                    {
                        name: 'status',
                        type: 'select',
                        placeholder: 'All Statuses',
                        options: ['pending', 'in_progress', 'completed', 'unrepairable'].map((s) => ({ value: s, label: s })),
                    },
                ]}
            />

            <DataTable
                columns={[
                    { key: 'asset', label: 'Asset', render: (r) => `${r.inventory?.asset_code} - ${r.inventory?.item_name}` },
                    { key: 'issue_title', label: 'Issue' },
                    { key: 'vendor', label: 'Vendor' },
                    { key: 'repair_cost', label: 'Cost' },
                    { key: 'sent_date', label: 'Sent' },
                    { key: 'return_date', label: 'Returned' },
                    { key: 'status', label: 'Status', render: (r) => <span className="capitalize">{r.status.replace('_', ' ')}</span> },
                ]}
                rows={records.data}
                actions={(row) => (
                    <Link href={route('maintenance.edit', row.id)} className="text-brand-600 hover:underline">
                        Update
                    </Link>
                )}
            />

            <Pagination links={records.links} />
        </AuthenticatedLayout>
    );
}
