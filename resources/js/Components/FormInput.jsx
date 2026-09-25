export default function FormInput({ label, error, className = '', required = false, ...props }) {
    return (
        <div className={className}>
            {label && (
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    {label} {required && <span className="text-red-500">*</span>}
                </label>
            )}
            <input
                {...props}
                className={`w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm ${
                    error ? 'border-red-400' : ''
                }`}
            />
            {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
        </div>
    );
}
