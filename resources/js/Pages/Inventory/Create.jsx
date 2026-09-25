import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InventoryForm from './InventoryForm';

export default function Create({ categories, locations }) {
    const { data, setData, post, processing, errors } = useForm({
        asset_type: 'fixed_asset',
        category_id: '',
        location_id: '',
        item_name: '',
        description: '',
        brand: '',
        model: '',
        serial_number: '',
        quantity: 1,
        condition: 'new',
        status: 'active',
        unit_cost: 0,
        supplier: '',
        invoice_number: '',
        purchase_date: '',
        received_date: '',
        payment_method: '',
        payment_date: '',
        warranty_start: '',
        warranty_end: '',
        it_details: {},
        depreciation: { method: 'SLM', useful_life: 5 },
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('inventory.store'));
    };

    return (
        <AuthenticatedLayout header="Add Inventory">
            <Head title="Add Inventory" />
            <form onSubmit={submit} className="space-y-6 max-w-4xl">
                <InventoryForm data={data} setData={setData} errors={errors} categories={categories} locations={locations} />
                <div className="flex justify-end gap-3">
                    <button
                        type="submit"
                        disabled={processing}
                        className="px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 disabled:opacity-50"
                    >
                        {processing ? 'Saving...' : 'Save Asset'}
                    </button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
