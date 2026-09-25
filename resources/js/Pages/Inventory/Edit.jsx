import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InventoryForm from './InventoryForm';

export default function Edit({ inventory, categories, locations }) {
    const { data, setData, put, processing, errors } = useForm({
        asset_type: inventory.asset_type,
        category_id: inventory.category_id,
        location_id: inventory.location_id,
        item_name: inventory.item_name,
        description: inventory.description || '',
        brand: inventory.brand || '',
        model: inventory.model || '',
        serial_number: inventory.serial_number || '',
        quantity: inventory.quantity,
        condition: inventory.condition,
        status: inventory.status,
        unit_cost: inventory.unit_cost,
        supplier: inventory.supplier || '',
        invoice_number: inventory.invoice_number || '',
        purchase_date: inventory.purchase_date || '',
        received_date: inventory.received_date || '',
        payment_method: inventory.payment_method || '',
        payment_date: inventory.payment_date || '',
        warranty_start: inventory.warranty_start || '',
        warranty_end: inventory.warranty_end || '',
        it_details: inventory.itDetail || {},
        depreciation: inventory.depreciation || { method: 'SLM', useful_life: 5 },
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('inventory.update', inventory.id));
    };

    return (
        <AuthenticatedLayout header={`Edit Asset - ${inventory.asset_code}`}>
            <Head title="Edit Asset" />
            <form onSubmit={submit} className="space-y-6 max-w-4xl">
                <InventoryForm data={data} setData={setData} errors={errors} categories={categories} locations={locations} />
                <div className="flex justify-end gap-3">
                    <button
                        type="submit"
                        disabled={processing}
                        className="px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 disabled:opacity-50"
                    >
                        {processing ? 'Saving...' : 'Update Asset'}
                    </button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
