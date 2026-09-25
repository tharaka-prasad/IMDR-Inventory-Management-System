import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import Pagination from '@/Components/Pagination';
import SearchFilter from '@/Components/SearchFilter';
import Modal from '@/Components/Modal';
import ConfirmDelete from '@/Components/ConfirmDelete';
import FormInput from '@/Components/FormInput';
import SelectInput from '@/Components/SelectInput';

const ASSET_TYPES = [
    { value: 'fixed_asset', label: 'Fixed Asset' },
    { value: 'it_asset', label: 'IT Asset' },
    { value: 'consumable', label: 'Consumable' },
];

export default function Index({ categories, filters }) {
    const [editing, setEditing] = useState(null); // null = closed, {} = create, {...} = edit
    const [toDelete, setToDelete] = useState(null);
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '', code: '', asset_type: 'fixed_asset', description: '',
    });

    function openCreate() {
        reset();
        setEditing({});
    }

    function openEdit(category) {
        setData({ name: category.name, code: category.code, asset_type: category.asset_type, description: category.description ?? '' });
        setEditing(category);
    }

    function submit(e) {
        e.preventDefault();
        if (editing?.id) {
            put(route('settings.categories.update', editing.id), { onSuccess: () => setEditing(null) });
        } else {
            post(route('settings.categories.store'), { onSuccess: () => setEditing(null) });
        }
    }

    return (
        <AuthenticatedLayout header="Category Management">
            <Head title="Categories" />

            <div className="flex justify-end mb-4">
                <button onClick={openCreate} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700">
                    + Add Category
                </button>
            </div>

            <SearchFilter routeName={route('settings.categories.index')} initial={filters} fields={[{ name: 'search', type: 'text', placeholder: 'Search categories...' }]} />

            <DataTable
                columns={[
                    { key: 'name', label: 'Name' },
                    { key: 'code', label: 'Code' },
                    { key: 'asset_type', label: 'Module', render: (r) => ASSET_TYPES.find((t) => t.value === r.asset_type)?.label ?? r.asset_type },
                    { key: 'inventories_count', label: 'Assets' },
                ]}
                rows={categories.data}
                actions={(row) => (
                    <div className="flex justify-end gap-2">
                        <button onClick={() => openEdit(row)} className="text-brand-600 hover:underline">Edit</button>
                        <button onClick={() => setToDelete(row)} className="text-red-600 hover:underline">Delete</button>
                    </div>
                )}
            />
            <Pagination links={categories.links} />

            <Modal show={!!editing} onClose={() => setEditing(null)} title={editing?.id ? 'Edit Category' : 'Add Category'} maxWidth="md">
                <form onSubmit={submit} className="space-y-4">
                    <FormInput label="Name" value={data.name} onChange={(e) => setData('name', e.target.value)} error={errors.name} required autoFocus />
                    <FormInput label="Code (used in asset codes, e.g. IT, FUR)" value={data.code} onChange={(e) => setData('code', e.target.value.toUpperCase())} error={errors.code} required />
                    <SelectInput label="Module" value={data.asset_type} onChange={(e) => setData('asset_type', e.target.value)} error={errors.asset_type} required>
                        {ASSET_TYPES.map((t) => <option key={t.value} value={t.value}>{t.label}</option>)}
                    </SelectInput>
                    <FormInput label="Description" value={data.description} onChange={(e) => setData('description', e.target.value)} error={errors.description} />
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
                onConfirm={() => router.delete(route('settings.categories.destroy', toDelete.id), { onFinish: () => setToDelete(null) })}
                title={`Delete category "${toDelete?.name}"?`}
                description="Categories with existing assets cannot be deleted."
            />
        </AuthenticatedLayout>
    );
}
