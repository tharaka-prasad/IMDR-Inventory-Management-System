import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import Pagination from '@/Components/Pagination';
import SearchFilter from '@/Components/SearchFilter';

export default function Index({ admins, filters }) {
    function toggleStatus(admin) {
        router.patch(route('settings.admins.toggleStatus', admin.id), {}, { preserveScroll: true });
    }

    function changeRole(admin) {
        const role = admin.role === 'admin' ? 'assignee' : 'admin';
        if (confirm(`Change ${admin.full_name}'s role to ${role}?`)) {
            router.patch(route('settings.admins.changeRole', admin.id), { role }, { preserveScroll: true });
        }
    }

    return (
        <AuthenticatedLayout header="Admin Management">
            <Head title="Admin Management" />

            <div className="flex justify-end mb-4">
                <Link href={route('settings.admins.create')} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700">
                    + Create Admin
                </Link>
            </div>

            <SearchFilter routeName={route('settings.admins.index')} initial={filters} fields={[{ name: 'search', type: 'text', placeholder: 'Search by name...' }]} />

            <DataTable
                columns={[
                    { key: 'full_name', label: 'Name' },
                    { key: 'email', label: 'Email' },
                    { key: 'phone', label: 'Phone' },
                    { key: 'status', label: 'Status', render: (r) => (
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${r.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{r.status}</span>
                    ) },
                ]}
                rows={admins.data}
                actions={(row) => (
                    <div className="flex justify-end gap-2">
                        <Link href={route('settings.admins.edit', row.id)} className="text-brand-600 hover:underline">Edit</Link>
                        <button onClick={() => changeRole(row)} className="text-purple-600 hover:underline">Change Role</button>
                        <button onClick={() => toggleStatus(row)} className="text-amber-600 hover:underline">
                            {row.status === 'active' ? 'Disable' : 'Enable'}
                        </button>
                    </div>
                )}
            />
            <Pagination links={admins.links} />
        </AuthenticatedLayout>
    );
}
