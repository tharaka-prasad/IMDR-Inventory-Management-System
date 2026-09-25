import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import SelectInput from '@/Components/SelectInput';
import FormInput from '@/Components/FormInput';

export default function Create({ assignments }) {
    const { data, setData, post, processing, errors } = useForm({
        assignment_id: '',
        quantity: 1,
        return_date: new Date().toISOString().slice(0, 10),
        condition: 'good',
        remarks: '',
    });

    const selected = assignments.find((a) => String(a.id) === String(data.assignment_id));

    const submit = (e) => {
        e.preventDefault();
        post(route('return.store'));
    };

    return (
        <AuthenticatedLayout header="Return Product">
            <Head title="Return Product" />
            <form onSubmit={submit} className="max-w-xl space-y-4 bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <SelectInput
                    label="Select Assignment"
                    required
                    value={data.assignment_id}
                    onChange={(e) => setData('assignment_id', e.target.value)}
                    error={errors.assignment_id}
                >
                    <option value="">Choose an outstanding assignment</option>
                    {assignments.map((a) => (
                        <option key={a.id} value={a.id}>
                            {a.inventory?.asset_code} - {a.inventory?.item_name} → {a.assignee?.full_name} ({a.outstanding_quantity} outstanding)
                        </option>
                    ))}
                </SelectInput>

                <FormInput
                    label="Return Quantity"
                    type="number"
                    min="1"
                    max={selected?.outstanding_quantity}
                    required
                    value={data.quantity}
                    onChange={(e) => setData('quantity', e.target.value)}
                    error={errors.quantity}
                />

                <SelectInput
                    label="Condition"
                    required
                    value={data.condition}
                    onChange={(e) => setData('condition', e.target.value)}
                    error={errors.condition}
                >
                    {['new', 'good', 'fair', 'damaged'].map((c) => (
                        <option key={c} value={c}>{c}</option>
                    ))}
                </SelectInput>

                <FormInput
                    label="Return Date"
                    type="date"
                    required
                    value={data.return_date}
                    onChange={(e) => setData('return_date', e.target.value)}
                    error={errors.return_date}
                />

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea
                        value={data.remarks}
                        onChange={(e) => setData('remarks', e.target.value)}
                        rows={3}
                        className="w-full rounded-lg border-gray-300 shadow-sm text-sm"
                    />
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 disabled:opacity-50"
                >
                    {processing ? 'Recording...' : 'Record Return'}
                </button>
            </form>
        </AuthenticatedLayout>
    );
}
