import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ reportTypes }) {
    return (
        <AuthenticatedLayout header="Reports">
            <Head title="Reports" />
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {Object.entries(reportTypes).map(([key, title]) => (
                    <div key={key} className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <h3 className="text-sm font-semibold text-gray-900 mb-3">{title}</h3>
                        <div className="flex gap-2 flex-wrap">
                            <Link
                                href={route('reports.show', key)}
                                className="px-3 py-1.5 text-xs font-medium text-brand-700 bg-brand-50 rounded-md hover:bg-brand-100"
                            >
                                View
                            </Link>
                            <a
                                href={route('reports.show', { type: key, format: 'pdf' })}
                                className="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                PDF
                            </a>
                            <a
                                href={route('reports.show', { type: key, format: 'excel' })}
                                className="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Excel
                            </a>
                        </div>
                    </div>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}
