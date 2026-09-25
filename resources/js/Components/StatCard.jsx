export default function StatCard({ label, value, accent = 'brand', hint }) {
    const accents = {
        brand: 'bg-brand-50 text-brand-700',
        green: 'bg-green-50 text-green-700',
        orange: 'bg-orange-50 text-orange-700',
        purple: 'bg-purple-50 text-purple-700',
    };

    return (
        <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div className={`inline-block text-xs font-medium px-2 py-1 rounded-md mb-3 ${accents[accent]}`}>
                {label}
            </div>
            <div className="text-2xl font-bold text-gray-900">{value}</div>
            {hint && <div className="text-xs text-gray-500 mt-1">{hint}</div>}
        </div>
    );
}
