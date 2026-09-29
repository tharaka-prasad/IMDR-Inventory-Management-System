import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import Pagination from '@/Components/Pagination';
import { formatDate } from '@/utils/date';

export default function Index({ returns }) {
    return (
        <AuthenticatedLayout header="Return Products">
            <Head title="Return Products" />

            <div className="flex justify-end mb-4">
                <Link href={route('return.create')} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700">
                    + Record Return
                </Link>
            </div>

            <DataTable
                columns={[
                    { key: 'asset', label: 'Asset', render: (r) => `${r.assignment?.inventory?.asset_code} - ${r.assignment?.inventory?.item_name}` },
                    { key: 'assignee', label: 'Returned By', render: (r) => r.assignment?.assignee?.full_name },
                    { key: 'quantity', label: 'Qty' },
                    { key: 'return_date', label: 'Return Date', render: (r) => formatDate(r.return_date) },
                    { key: 'condition', label: 'Condition' },
                    { key: 'received_by', label: 'Received By', render: (r) => r.received_by?.full_name },
                ]}
                rows={returns.data}
            />

            <Pagination links={returns.links} />
        </AuthenticatedLayout> 
    );
}
