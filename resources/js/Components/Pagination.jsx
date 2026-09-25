import { Link } from '@inertiajs/react';

export default function Pagination({ links }) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="flex flex-wrap items-center gap-1 mt-4">
            {links.map((link, i) => (
                <Link
                    key={i}
                    href={link.url || '#'}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                    preserveScroll
                    className={`px-3 py-1.5 text-sm rounded-md border ${
                        link.active
                            ? 'bg-brand-600 text-white border-brand-600'
                            : link.url
                            ? 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
                            : 'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed'
                    }`}
                />
            ))}
        </div>
    );
}
