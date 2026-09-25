import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import SearchFilter from '@/Components/SearchFilter';
import Pagination from '@/Components/Pagination';

export default function Index({ assignments, filters }) {
    return (
        <AuthenticatedLayout header="Issue Products">
            <Head title="Issue Products" />

            <div className="flex justify-end mb-4">
                <Link href={route('issue.create')} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700">
                    + Issue Product
                </Link>
            </div>

            <SearchFilter
                routeName={route('issue.index')}
                initial={filters}
                fields={[
                    { name: 'search', type: 'text', placeholder: 'Search item, asset code, assignee...' },
                    {
                        name: 'status',
                        type: 'select',
                        placeholder: 'All Statuses',
                        options: ['issued', 'partially_returned', 'returned', 'transferred'].map((s) => ({ value: s, label: s })),
                    },
                ]}
            />

            <DataTable
                columns={[
                    { key: 'asset_code', label: 'Asset', render: (r) => `${r.inventory?.asset_code} - ${r.inventory?.item_name}` },
                    { key: 'assignee', label: 'Assigned To', render: (r) => r.assignee?.full_name },
                    { key: 'quantity', label: 'Qty' },
                    { key: 'issue_date', label: 'Issue Date' },
                    { key: 'issuedBy', label: 'Given By', render: (r) => r.issuedBy?.full_name },
                    { key: 'status', label: 'Status', render: (r) => <span className="capitalize">{r.status.replace('_', ' ')}</span> },
                ]}
                rows={assignments.data}
            />

            <Pagination links={assignments.links} />
        </AuthenticatedLayout>
    );
}
