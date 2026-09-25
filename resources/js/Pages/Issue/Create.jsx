import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import SelectInput from '@/Components/SelectInput';
import FormInput from '@/Components/FormInput';

export default function Create({ products, users }) {
    const { data, setData, post, processing, errors } = useForm({
        inventory_id: '',
        assigned_to: '',
        quantity: 1,
        issue_date: new Date().toISOString().slice(0, 10),
        remarks: '',
    });

    const selectedProduct = products.find((p) => String(p.id) === String(data.inventory_id));

    const submit = (e) => {
        e.preventDefault();
        post(route('issue.store'));
    };

    return (
        <AuthenticatedLayout header="Issue Product">
            <Head title="Issue Product" />
            <form onSubmit={submit} className="max-w-xl space-y-4 bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <SelectInput
                    label="Select Product"
                    required
                    value={data.inventory_id}
                    onChange={(e) => setData('inventory_id', e.target.value)}
                    error={errors.inventory_id}
                >
                    <option value="">Choose a product already in inventory</option>
                    {products.map((p) => (
                        <option key={p.id} value={p.id}>
                            {p.asset_code} - {p.item_name} ({p.available_quantity} available)
                        </option>
                    ))}
                </SelectInput>

                {selectedProduct && (
                    <p className="text-xs text-gray-500 -mt-2">
                        Available Qty: <strong>{selectedProduct.available_quantity}</strong>
                    </p>
                )}

                <SelectInput
                    label="Assign To"
                    required
                    value={data.assigned_to}
                    onChange={(e) => setData('assigned_to', e.target.value)}
                    error={errors.assigned_to}
                >
                    <option value="">Choose a user</option>
                    {users.map((u) => (
                        <option key={u.id} value={u.id}>
                            {u.full_name} ({u.role})
                        </option>
                    ))}
                </SelectInput>

                <FormInput
                    label="Quantity"
                    type="number"
                    min="1"
                    max={selectedProduct?.available_quantity}
                    required
                    value={data.quantity}
                    onChange={(e) => setData('quantity', e.target.value)}
                    error={errors.quantity}
                />

                <FormInput
                    label="Issue Date"
                    type="date"
                    required
                    value={data.issue_date}
                    onChange={(e) => setData('issue_date', e.target.value)}
                    error={errors.issue_date}
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
                    {processing ? 'Issuing...' : 'Issue Product'}
                </button>
            </form>
        </AuthenticatedLayout>
    );
}
