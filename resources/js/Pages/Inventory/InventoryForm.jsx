import FormInput from '@/Components/FormInput';
import SelectInput from '@/Components/SelectInput';

export default function InventoryForm({ data, setData, errors, categories, locations }) {
    const set = (key) => (e) => setData(key, e.target.value);
    const setNested = (group, key) => (e) => setData(group, { ...data[group], [key]: e.target.value });

    return (
        <div className="space-y-8">
            {/* Asset Information */}
            <Section title="Asset Information">
                <SelectInput label="Asset Type" required value={data.asset_type} onChange={set('asset_type')} error={errors.asset_type}>
                    <option value="fixed_asset">Fixed Asset</option>
                    <option value="it_asset">IT Asset</option>
                    <option value="consumable">Consumable / Inventory</option>
                </SelectInput>
                <SelectInput label="Category" required value={data.category_id} onChange={set('category_id')} error={errors.category_id}>
                    <option value="">Select category</option>
                    {categories.map((c) => (
                        <option key={c.id} value={c.id}>{c.name} ({c.code})</option>
                    ))}
                </SelectInput>
                <SelectInput label="Location" required value={data.location_id} onChange={set('location_id')} error={errors.location_id}>
                    <option value="">Select location</option>
                    {locations.map((l) => (
                        <option key={l.id} value={l.id}>{l.name}{l.building ? ` - ${l.building}` : ''}</option>
                    ))}
                </SelectInput>
                <FormInput label="Item Name" required value={data.item_name} onChange={set('item_name')} error={errors.item_name} />
                <FormInput label="Brand" value={data.brand} onChange={set('brand')} error={errors.brand} />
                <FormInput label="Model" value={data.model} onChange={set('model')} error={errors.model} />
                <FormInput label="Serial Number" value={data.serial_number} onChange={set('serial_number')} error={errors.serial_number} />
                <FormInput label="Quantity" type="number" min="1" required value={data.quantity} onChange={set('quantity')} error={errors.quantity} />
                <SelectInput label="Condition" required value={data.condition} onChange={set('condition')} error={errors.condition}>
                    {['new', 'good', 'fair', 'damaged', 'disposed'].map((v) => <option key={v} value={v}>{v}</option>)}
                </SelectInput>
                <SelectInput label="Status" required value={data.status} onChange={set('status')} error={errors.status}>
                    {['active', 'in_maintenance', 'disposed', 'inactive'].map((v) => <option key={v} value={v}>{v}</option>)}
                </SelectInput>
                <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea
                        value={data.description || ''}
                        onChange={set('description')}
                        rows={3}
                        className="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm"
                    />
                </div>
            </Section>

            {/* Purchase Information */}
            <Section title="Purchase Information">
                <FormInput label="Unit Cost" type="number" step="0.01" min="0" required value={data.unit_cost} onChange={set('unit_cost')} error={errors.unit_cost} />
                <FormInput label="Supplier" value={data.supplier} onChange={set('supplier')} error={errors.supplier} />
                <FormInput label="Invoice Number" value={data.invoice_number} onChange={set('invoice_number')} error={errors.invoice_number} />
                <FormInput label="Purchase Date" type="date" value={data.purchase_date} onChange={set('purchase_date')} error={errors.purchase_date} />
                <FormInput label="Received Date" type="date" value={data.received_date} onChange={set('received_date')} error={errors.received_date} />
                <FormInput label="Payment Method" value={data.payment_method} onChange={set('payment_method')} error={errors.payment_method} />
                <FormInput label="Payment Date" type="date" value={data.payment_date} onChange={set('payment_date')} error={errors.payment_date} />
            </Section>

            {/* Warranty */}
            <Section title="Warranty">
                <FormInput label="Warranty Start" type="date" value={data.warranty_start} onChange={set('warranty_start')} error={errors.warranty_start} />
                <FormInput label="Warranty End" type="date" value={data.warranty_end} onChange={set('warranty_end')} error={errors['warranty_end']} />
            </Section>

            {/* IT Details - only relevant for IT assets */}
            {data.asset_type === 'it_asset' && (
                <Section title="IT Details">
                    <FormInput label="Device Name" value={data.it_details.device_name} onChange={setNested('it_details', 'device_name')} />
                    <FormInput label="Hostname" value={data.it_details.hostname} onChange={setNested('it_details', 'hostname')} />
                    <FormInput label="CPU" value={data.it_details.cpu} onChange={setNested('it_details', 'cpu')} />
                    <FormInput label="RAM" value={data.it_details.ram} onChange={setNested('it_details', 'ram')} />
                    <FormInput label="Storage" value={data.it_details.storage} onChange={setNested('it_details', 'storage')} />
                    <FormInput label="GPU" value={data.it_details.gpu} onChange={setNested('it_details', 'gpu')} />
                    <FormInput label="Operating System" value={data.it_details.operating_system} onChange={setNested('it_details', 'operating_system')} />
                    <FormInput label="MAC Address" value={data.it_details.mac_address} onChange={setNested('it_details', 'mac_address')} />
                    <FormInput label="IP Address" value={data.it_details.ip_address} onChange={setNested('it_details', 'ip_address')} />
                    <FormInput label="Monitor Size" value={data.it_details.monitor_size} onChange={setNested('it_details', 'monitor_size')} />
                    <FormInput label="Printer Type" value={data.it_details.printer_type} onChange={setNested('it_details', 'printer_type')} />
                    <FormInput label="Printer IP" value={data.it_details.printer_ip} onChange={setNested('it_details', 'printer_ip')} />
                    <FormInput label="License Reference" value={data.it_details.license_reference} onChange={setNested('it_details', 'license_reference')} />
                    <FormInput label="Antivirus" value={data.it_details.antivirus} onChange={setNested('it_details', 'antivirus')} />
                    <div className="md:col-span-2">
                        <label className="block text-sm font-medium text-gray-700 mb-1">Software Installed</label>
                        <textarea value={data.it_details.software_installed || ''} onChange={setNested('it_details', 'software_installed')} rows={2} className="w-full rounded-lg border-gray-300 shadow-sm text-sm" />
                    </div>
                    <div className="md:col-span-2">
                        <label className="block text-sm font-medium text-gray-700 mb-1">IT Notes</label>
                        <textarea value={data.it_details.it_notes || ''} onChange={setNested('it_details', 'it_notes')} rows={2} className="w-full rounded-lg border-gray-300 shadow-sm text-sm" />
                    </div>
                </Section>
            )}

            {/* Depreciation */}
            <Section title="Depreciation">
                <SelectInput label="Method" value={data.depreciation.method} onChange={setNested('depreciation', 'method')}>
                    <option value="SLM">Straight Line Method (SLM)</option>
                    <option value="WDV">Written Down Value (WDV)</option>
                </SelectInput>
                <FormInput label="Useful Life (years)" type="number" min="1" value={data.depreciation.useful_life} onChange={setNested('depreciation', 'useful_life')} />
                <FormInput label="Disposal Date" type="date" value={data.depreciation.disposal_date} onChange={setNested('depreciation', 'disposal_date')} />
                <FormInput label="Disposal Reason" value={data.depreciation.disposal_reason} onChange={setNested('depreciation', 'disposal_reason')} />
                <FormInput label="Disposal Value" type="number" step="0.01" value={data.depreciation.disposal_value} onChange={setNested('depreciation', 'disposal_value')} />
                <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea value={data.depreciation.remarks || ''} onChange={setNested('depreciation', 'remarks')} rows={2} className="w-full rounded-lg border-gray-300 shadow-sm text-sm" />
                </div>
                <p className="md:col-span-2 text-xs text-gray-500">
                    Net Book Value is calculated automatically from unit cost, method, and useful life — no need to enter it manually.
                </p>
            </Section>
        </div>
    );
}

function Section({ title, children }) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 className="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wide">{title}</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">{children}</div>
        </div>
    );
}
