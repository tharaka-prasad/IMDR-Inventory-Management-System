import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import MaintenanceForm from './MaintenanceForm';

export default function Create({ inventories }) {
    const { data, setData, post, processing, errors } = useForm({
        inventory_id: '',
        issue_title: '',
        description: '',
        vendor: '',
        repair_cost: 0,
        sent_date: new Date().toISOString().slice(0, 10),
        return_date: '',
        status: 'pending',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('maintenance.store'));
    };

    return (
        <AuthenticatedLayout header="Add Repair">
            <Head title="Add Repair" />
            <form onSubmit={submit} className="max-w-xl bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-4">
                <MaintenanceForm data={data} setData={setData} errors={errors} inventories={inventories} />
                <button
                    type="submit"
                    disabled={processing}
                    className="w-full px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 disabled:opacity-50"
                >
                    {processing ? 'Saving...' : 'Save Repair Record'}
                </button>
            </form>
        </AuthenticatedLayout>
    );
}
