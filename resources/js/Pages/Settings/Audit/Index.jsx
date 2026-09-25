import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import Pagination from '@/Components/Pagination';
import SearchFilter from '@/Components/SearchFilter';

export default function Index({ logs, filters, users }) {
    return (
        <AuthenticatedLayout header="Audit Logs">
            <Head title="Audit Logs" />

            <SearchFilter
                routeName={route('settings.audit.index')}
                initial={filters}
                fields={[
                    { name: 'user_id', type: 'select', placeholder: 'All Users', options: users.map((u) => ({ value: u.id, label: u.full_name })) },
                    { name: 'action', type: 'text', placeholder: 'Action (e.g. issued, login)' },
                    { name: 'from', type: 'text', placeholder: 'From (YYYY-MM-DD)' },
                    { name: 'to', type: 'text', placeholder: 'To (YYYY-MM-DD)' },
                ]}
            />

            <DataTable
                columns={[
                    { key: 'created_at', label: 'Date', render: (r) => new Date(r.created_at).toLocaleString() },
                    { key: 'user', label: 'User', render: (r) => r.user?.full_name ?? 'System' },
                    { key: 'action', label: 'Action' },
                    { key: 'table_name', label: 'Table' },
                    { key: 'record_id', label: 'Record' },
                    { key: 'ip_address', label: 'IP' },
                ]}
                rows={logs.data}
            />
            <Pagination links={logs.links} />
        </AuthenticatedLayout>
    );
}
