import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import MaintenanceForm from './MaintenanceForm';

export default function Edit({ maintenance, inventories }) {
    const { data, setData, put, processing, errors } = useForm({
        inventory_id: maintenance.inventory_id,
        issue_title: maintenance.issue_title,
        description: maintenance.description || '',
        vendor: maintenance.vendor || '',
        repair_cost: maintenance.repair_cost,
        sent_date: maintenance.sent_date || '',
        return_date: maintenance.return_date || '',
        status: maintenance.status,
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('maintenance.update', maintenance.id));
    };

    return (
        <AuthenticatedLayout header={`Update Repair - ${maintenance.inventory?.asset_code}`}>
            <Head title="Update Repair" />
            <form onSubmit={submit} className="max-w-xl bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-4">
                <MaintenanceForm data={data} setData={setData} errors={errors} inventories={inventories} />
                <button
                    type="submit"
                    disabled={processing}
                    className="w-full px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 disabled:opacity-50"
                >
                    {processing ? 'Saving...' : 'Update Repair Record'}
                </button>
            </form>
        </AuthenticatedLayout>
    );
}
