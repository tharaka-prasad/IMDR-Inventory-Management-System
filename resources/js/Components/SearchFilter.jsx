import { router } from '@inertiajs/react';
import { useState } from 'react';

/**
 * fields: [{ name, type: 'text'|'select', placeholder?, options?: [{value,label}] }]
 */
export default function SearchFilter({ routeName, initial = {}, fields }) {
    const [values, setValues] = useState(initial);

    const apply = (next) => {
        setValues(next);
        router.get(routeName, next, { preserveState: true, replace: true });
    };

    const reset = () => apply({});

    return (
        <div className="flex flex-wrap items-end gap-3 bg-white border border-gray-200 rounded-xl p-4 mb-4">
            {fields.map((field) => (
                <div key={field.name} className="min-w-[160px]">
                    {field.type === 'select' ? (
                        <select
                            value={values[field.name] || ''}
                            onChange={(e) => apply({ ...values, [field.name]: e.target.value })}
                            className="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option value="">{field.placeholder}</option>
                            {field.options.map((opt) => (
                                <option key={opt.value} value={opt.value}>
                                    {opt.label}
                                </option>
                            ))}
                        </select>
                    ) : (
                        <input
                            type="text"
                            value={values[field.name] || ''}
                            placeholder={field.placeholder}
                            onChange={(e) => setValues({ ...values, [field.name]: e.target.value })}
                            onKeyDown={(e) => e.key === 'Enter' && apply(values)}
                            className="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                    )}
                </div>
            ))}
            <button
                onClick={() => apply(values)}
                className="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700"
            >
                Search
            </button>
            <button
                onClick={reset}
                className="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200"
            >
                Reset
            </button>
        </div>
    );
}
