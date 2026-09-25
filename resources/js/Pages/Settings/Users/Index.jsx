import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import Pagination from '@/Components/Pagination';
import SearchFilter from '@/Components/SearchFilter';
import ConfirmDelete from '@/Components/ConfirmDelete';

export default function Index({ users, filters }) {
    const [toDelete, setToDelete] = useState(null);

    function toggleStatus(user) {
        router.patch(route('settings.users.toggleStatus', user.id), {}, { preserveScroll: true });
    }

    function resetPassword(user) {
        if (confirm(`Reset password for ${user.full_name}?`)) {
            router.post(route('settings.users.resetPassword', user.id), {}, { preserveScroll: true });
        }
    }

    return (
        <AuthenticatedLayout header="User Management (Assignees)">
            <Head title="User Management" />

            <div className="flex justify-end mb-4">
                <Link href={route('settings.users.create')} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700">
                    + Create Assignee
                </Link>
            </div>

            <SearchFilter routeName={route('settings.users.index')} initial={filters} fields={[{ name: 'search', type: 'text', placeholder: 'Search by name...' }]} />

            <DataTable
                columns={[
                    { key: 'full_name', label: 'Name' },
                    { key: 'email', label: 'Email' },
                    { key: 'phone', label: 'Phone' },
                    { key: 'status', label: 'Status', render: (r) => (
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${r.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{r.status}</span>
                    ) },
                ]}
                rows={users.data}
                actions={(row) => (
                    <div className="flex justify-end gap-2">
                        <Link href={route('settings.users.edit', row.id)} className="text-brand-600 hover:underline">Edit</Link>
                        <button onClick={() => resetPassword(row)} className="text-gray-500 hover:underline">Reset PW</button>
                        <button onClick={() => toggleStatus(row)} className="text-amber-600 hover:underline">
                            {row.status === 'active' ? 'Disable' : 'Enable'}
                        </button>
                        <button onClick={() => setToDelete(row)} className="text-red-600 hover:underline">Delete</button>
                    </div>
                )}
            />
            <Pagination links={users.links} />

            <ConfirmDelete
                show={!!toDelete}
                onClose={() => setToDelete(null)}
                onConfirm={() => router.delete(route('settings.users.destroy', toDelete.id), { onFinish: () => setToDelete(null) })}
                title={`Remove ${toDelete?.full_name}?`}
            />
        </AuthenticatedLayout>
    );
}
