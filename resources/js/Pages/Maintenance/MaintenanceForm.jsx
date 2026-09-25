import SelectInput from '@/Components/SelectInput';
import FormInput from '@/Components/FormInput';

export default function MaintenanceForm({ data, setData, errors, inventories }) {
    const set = (key) => (e) => setData(key, e.target.value);

    return (
        <div className="space-y-4">
            <SelectInput label="Asset" required value={data.inventory_id} onChange={set('inventory_id')} error={errors.inventory_id}>
                <option value="">Select asset</option>
                {inventories.map((i) => (
                    <option key={i.id} value={i.id}>{i.asset_code} - {i.item_name}</option>
                ))}
            </SelectInput>
            <FormInput label="Issue Title" required value={data.issue_title} onChange={set('issue_title')} error={errors.issue_title} />
            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea value={data.description} onChange={set('description')} rows={3} className="w-full rounded-lg border-gray-300 shadow-sm text-sm" />
            </div>
            <FormInput label="Vendor" value={data.vendor} onChange={set('vendor')} error={errors.vendor} />
            <FormInput label="Repair Cost" type="number" step="0.01" min="0" value={data.repair_cost} onChange={set('repair_cost')} error={errors.repair_cost} />
            <FormInput label="Sent Date" type="date" value={data.sent_date} onChange={set('sent_date')} error={errors.sent_date} />
            <FormInput label="Return Date" type="date" value={data.return_date} onChange={set('return_date')} error={errors.return_date} />
            <SelectInput label="Status" required value={data.status} onChange={set('status')} error={errors.status}>
                {['pending', 'in_progress', 'completed', 'unrepairable'].map((s) => (
                    <option key={s} value={s}>{s}</option>
                ))}
            </SelectInput>
        </div>
    );
}
