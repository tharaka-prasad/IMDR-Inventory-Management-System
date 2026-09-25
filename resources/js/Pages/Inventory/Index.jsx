import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import SearchFilter from '@/Components/SearchFilter';
import Pagination from '@/Components/Pagination';
import ConfirmDelete from '@/Components/ConfirmDelete';

export default function Index({ inventories, filters, categories, locations }) {
    const { auth } = usePage().props;
    const canManage = auth.user.role === 'super_admin' || auth.user.role === 'admin';
    const [toDelete, setToDelete] = useState(null);

    const confirmDelete = () => {
        router.delete(route('inventory.destroy', toDelete.id), { onSuccess: () => setToDelete(null) });
    };

    return (
        <AuthenticatedLayout header="Inventory">
            <Head title="Inventory" />

            <div className="flex justify-between items-center mb-4">
                <p className="text-sm text-gray-500">Search by name, asset code, QR code, or serial number.</p>
                {canManage && (
                    <Link
                        href={route('inventory.create')}
                        className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700"
                    >
                        + Add Inventory
                    </Link>
                )}
            </div>

            <SearchFilter
                routeName={route('inventory.index')}
                initial={filters}
                fields={[
                    { name: 'search', type: 'text', placeholder: 'Search item, code, serial...' },
                    { name: 'qr_code', type: 'text', placeholder: 'QR code' },
                    {
                        name: 'category_id',
                        type: 'select',
                        placeholder: 'All Categories',
                        options: categories.map((c) => ({ value: c.id, label: c.name })),
                    },
                    {
                        name: 'location_id',
                        type: 'select',
                        placeholder: 'All Locations',
                        options: locations.map((l) => ({ value: l.id, label: l.name })),
                    },
                    {
                        name: 'asset_type',
                        type: 'select',
                        placeholder: 'All Types',
                        options: [
                            { value: 'fixed_asset', label: 'Fixed Asset' },
                            { value: 'it_asset', label: 'IT Asset' },
                            { value: 'consumable', label: 'Consumable' },
                        ],
                    },
                ]}
            />

            <DataTable
                columns={[
                    { key: 'asset_code', label: 'Asset Code' },
                    { key: 'item_name', label: 'Item' },
                    { key: 'category', label: 'Category', render: (r) => r.category?.name },
                    { key: 'location', label: 'Location', render: (r) => r.location?.name },
                    { key: 'available_quantity', label: 'Available', render: (r) => `${r.available_quantity} / ${r.quantity}` },
                    {
                        key: 'status',
                        label: 'Status',
                        render: (r) => <span className="capitalize">{r.status.replace('_', ' ')}</span>,
                    },
                ]}
                rows={inventories.data}
                actions={(row) => (
                    <div className="flex justify-end gap-3">
                        <Link href={route('inventory.show', row.id)} className="text-brand-600 hover:underline">
                            View
                        </Link>
                        {canManage && (
                            <>
                                <Link href={route('inventory.edit', row.id)} className="text-gray-600 hover:underline">
                                    Edit
                                </Link>
                                <button onClick={() => setToDelete(row)} className="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </>
                        )}
                    </div>
                )}
            />

            <Pagination links={inventories.links} />

            <ConfirmDelete
                show={!!toDelete}
                onClose={() => setToDelete(null)}
                onConfirm={confirmDelete}
                title={`Delete ${toDelete?.asset_code}?`}
            />
        </AuthenticatedLayout>
    );
}
