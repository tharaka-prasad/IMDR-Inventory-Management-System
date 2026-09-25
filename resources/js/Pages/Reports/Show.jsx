import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Show({ type, title, headings, rows, filters }) {
    const [from, setFrom] = useState(filters.from || '');
    const [to, setTo] = useState(filters.to || '');

    const applyFilter = () => {
        router.get(route('reports.show', type), { from, to }, { preserveState: true });
    };

    return (
        <AuthenticatedLayout header={title}>
            <Head title={title} />

            <div className="flex flex-wrap items-end gap-3 bg-white border border-gray-200 rounded-xl p-4 mb-4">
                <div>
                    <label className="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" value={from} onChange={(e) => setFrom(e.target.value)} className="rounded-lg border-gray-300 text-sm" />
                </div>
                <div>
                    <label className="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" value={to} onChange={(e) => setTo(e.target.value)} className="rounded-lg border-gray-300 text-sm" />
                </div>
                <button onClick={applyFilter} className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700">
                    Apply
                </button>
                <div className="flex-1" />
                <a
                    href={route('reports.show', { type, format: 'pdf', from, to })}
                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                >
                    Download PDF
                </a>
                <a
                    href={route('reports.show', { type, format: 'excel', from, to })}
                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                >
                    Download Excel
                </a>
            </div>

            <div className="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
                <table className="min-w-full divide-y divide-gray-200 text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            {headings.map((h) => (
                                <th key={h} className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    {h}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {rows.length === 0 && (
                            <tr>
                                <td colSpan={headings.length} className="px-4 py-8 text-center text-gray-400">
                                    No data for this range.
                                </td>
                            </tr>
                        )}
                        {rows.map((row, i) => (
                            <tr key={i} className="hover:bg-gray-50">
                                {row.map((cell, j) => (
                                    <td key={j} className="px-4 py-3 text-gray-700 whitespace-nowrap">{cell}</td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </AuthenticatedLayout>
    );
}
