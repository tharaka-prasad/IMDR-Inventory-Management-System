import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import Pagination from '@/Components/Pagination';
import SearchFilter from '@/Components/SearchFilter';
import Modal from '@/Components/Modal';
import ConfirmDelete from '@/Components/ConfirmDelete';
import FormInput from '@/Components/FormInput';

export default function Index({ locations, filters }) {
    const [editing, setEditing] = useState(null);
    const [toDelete, setToDelete] = useState(null);
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '', building: '', department: '',
    });

    function openCreate() {
        reset();
        setEditing({});
    }

    function openEdit(location) {
        setData({ name: location.name, building: location.building ?? '', department: location.department ?? '' });
        setEditing(location);
    }

    function submit(e) {
        e.preventDefault();
        if (editing?.id) {
            put(route('settings.locations.update', editing.id), { onSuccess: () => setEditing(null) });
        } else {
            post(route('settings.locations.store'), { onSuccess: () => setEditing(null) });
        }
    }

    return (
        <AuthenticatedLayout header="Location Management">
            <Head title="Locations" />

            <div className="flex justify-end mb-4">
                <button onClick={openCreate} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700">
                    + Add Location
                </button>
            </div>

            <SearchFilter routeName={route('settings.locations.index')} initial={filters} fields={[{ name: 'search', type: 'text', placeholder: 'Search locations...' }]} />

            <DataTable
                columns={[
                    { key: 'name', label: 'Name' },
                    { key: 'building', label: 'Building' },
                    { key: 'department', label: 'Department' },
                    { key: 'inventories_count', label: 'Assets' },
                ]}
                rows={locations.data}
                actions={(row) => (
                    <div className="flex justify-end gap-2">
                        <button onClick={() => openEdit(row)} className="text-brand-600 hover:underline">Edit</button>
                        <button onClick={() => setToDelete(row)} className="text-red-600 hover:underline">Delete</button>
                    </div>
                )}
            />
            <Pagination links={locations.links} />

            <Modal show={!!editing} onClose={() => setEditing(null)} title={editing?.id ? 'Edit Location' : 'Add Location'} maxWidth="md">
                <form onSubmit={submit} className="space-y-4">
                    <FormInput label="Name" value={data.name} onChange={(e) => setData('name', e.target.value)} error={errors.name} required autoFocus />
                    <FormInput label="Building" value={data.building} onChange={(e) => setData('building', e.target.value)} error={errors.building} />
                    <FormInput label="Department" value={data.department} onChange={(e) => setData('department', e.target.value)} error={errors.department} />
                    <div className="flex justify-end gap-3">
                        <button type="button" onClick={() => setEditing(null)} className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" disabled={processing} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 disabled:opacity-50">
                            {editing?.id ? 'Save' : 'Create'}
                        </button>
                    </div>
                </form>
            </Modal>

            <ConfirmDelete
                show={!!toDelete}
                onClose={() => setToDelete(null)}
                onConfirm={() => router.delete(route('settings.locations.destroy', toDelete.id), { onFinish: () => setToDelete(null) })}
                title={`Delete location "${toDelete?.name}"?`}
                description="Locations with existing assets cannot be deleted."
            />
        </AuthenticatedLayout>
    );
}
