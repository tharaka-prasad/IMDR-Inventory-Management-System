import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import QRCard from '@/Components/QRCard';
import DataTable from '@/Components/DataTable';

export default function Show({ inventory }) {
    const { auth } = usePage().props;
    const canManage = auth.user.role === 'super_admin' || auth.user.role === 'admin';

    return (
        <AuthenticatedLayout header={`${inventory.item_name} (${inventory.asset_code})`}>
            <Head title={inventory.asset_code} />

            <div className="flex justify-between items-start mb-6">
                <div className="flex gap-2 flex-wrap">
                    <Badge label={inventory.status} tone="brand" />
                    <Badge label={inventory.condition} tone="gray" />
                    <Badge label={inventory.asset_type.replace('_', ' ')} tone="purple" />
                </div>
                {canManage && (
                    <Link
                        href={route('inventory.edit', inventory.id)}
                        className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700"
                    >
                        Edit Asset
                    </Link>
                )}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 space-y-6">
                    <InfoCard title="Asset Information">
                        <Info label="Category" value={inventory.category?.name} />
                        <Info label="Location" value={inventory.location?.name} />
                        <Info label="Brand / Model" value={`${inventory.brand || '-'} / ${inventory.model || '-'}`} />
                        <Info label="Serial Number" value={inventory.serial_number} />
                        <Info label="Quantity" value={`${inventory.available_quantity} available of ${inventory.quantity}`} />
                        <Info label="Unit Cost" value={inventory.unit_cost} />
                        <Info label="Total Cost" value={inventory.total_cost} />
                        <Info label="Supplier" value={inventory.supplier} />
                        <Info label="Purchase Date" value={inventory.purchase_date} />
                        <Info label="Warranty" value={`${inventory.warranty_start || '-'} to ${inventory.warranty_end || '-'}`} />
                    </InfoCard>

                    {inventory.asset_type === 'it_asset' && inventory.itDetail && (
                        <InfoCard title="IT Details">
                            <Info label="Hostname" value={inventory.itDetail.hostname} />
                            <Info label="CPU" value={inventory.itDetail.cpu} />
                            <Info label="RAM" value={inventory.itDetail.ram} />
                            <Info label="Storage" value={inventory.itDetail.storage} />
                            <Info label="OS" value={inventory.itDetail.operating_system} />
                            <Info label="MAC Address" value={inventory.itDetail.mac_address} />
                            <Info label="IP Address" value={inventory.itDetail.ip_address} />
                            <Info label="Antivirus" value={inventory.itDetail.antivirus} />
                        </InfoCard>
                    )}

                    {inventory.depreciation && (
                        <InfoCard title="Depreciation">
                            <Info label="Method" value={inventory.depreciation.method} />
                            <Info label="Useful Life" value={`${inventory.depreciation.useful_life || '-'} years`} />
                            <Info label="Accumulated Depreciation" value={inventory.depreciation.accumulated_depreciation} />
                            <Info label="Net Book Value" value={inventory.depreciation.net_book_value} />
                        </InfoCard>
                    )}

                    <div>
                        <h3 className="text-sm font-semibold text-gray-900 mb-2">Assignment History</h3>
                        <DataTable
                            columns={[
                                { key: 'assignee', label: 'Assigned To', render: (r) => r.assignee?.full_name },
                                { key: 'quantity', label: 'Qty' },
                                { key: 'issue_date', label: 'Issue Date' },
                                { key: 'status', label: 'Status' },
                            ]}
                            rows={inventory.assignments || []}
                        />
                    </div>

                    <div>
                        <h3 className="text-sm font-semibold text-gray-900 mb-2">Stock History</h3>
                        <DataTable
                            columns={[
                                { key: 'action', label: 'Action' },
                                { key: 'quantity', label: 'Qty Change' },
                                { key: 'balance', label: 'Balance' },
                                { key: 'user', label: 'By', render: (r) => r.user?.full_name },
                                { key: 'created_at', label: 'Date' },
                            ]}
                            rows={inventory.stockHistories || []}
                        />
                    </div>
                </div>

                <div className="space-y-6">
                    <QRCard qrCode={inventory.qr_code} assetCode={inventory.asset_code} imageUrl={`/storage/qrcodes/${inventory.qr_code}.png`} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function InfoCard({ title, children }) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h3 className="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wide">{title}</h3>
            <div className="grid grid-cols-2 gap-4">{children}</div>
        </div>
    );
}

function Info({ label, value }) {
    return (
        <div>
            <div className="text-xs text-gray-500">{label}</div>
            <div className="text-sm text-gray-900">{value ?? '-'}</div>
        </div>
    );
}

function Badge({ label, tone }) {
    const tones = {
        brand: 'bg-brand-50 text-brand-700',
        gray: 'bg-gray-100 text-gray-700',
        purple: 'bg-purple-50 text-purple-700',
    };
    return <span className={`text-xs font-medium px-2.5 py-1 rounded-full capitalize ${tones[tone]}`}>{label}</span>;
}
