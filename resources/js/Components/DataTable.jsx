/**
 * columns: [{ key, label, render?(row) }]
 * rows: array of records
 * actions?(row): ReactNode - rendered in the trailing "Actions" column
 */
export default function DataTable({ columns, rows, actions, emptyMessage = 'No records found.' }) {
    return (
        <div className="overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
            <table className="min-w-full divide-y divide-gray-200 text-sm">
                <thead className="bg-gray-50">
                    <tr>
                        {columns.map((col) => (
                            <th
                                key={col.key}
                                className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                            >
                                {col.label}
                            </th>
                        ))}
                        {actions && (
                            <th className="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Actions
                            </th>
                        )}
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                    {rows.length === 0 && (
                        <tr>
                            <td colSpan={columns.length + (actions ? 1 : 0)} className="px-4 py-8 text-center text-gray-400">
                                {emptyMessage}
                            </td>
                        </tr>
                    )}
                    {rows.map((row, i) => (
                        <tr key={row.id ?? i} className="hover:bg-gray-50">
                            {columns.map((col) => (
                                <td key={col.key} className="px-4 py-3 text-gray-700 whitespace-nowrap">
                                    {col.render ? col.render(row) : row[col.key]}
                                </td>
                            ))}
                            {actions && (
                                <td className="px-4 py-3 text-right whitespace-nowrap">{actions(row)}</td>
                            )}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
