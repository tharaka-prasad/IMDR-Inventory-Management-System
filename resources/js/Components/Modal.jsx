export default function Modal({ show, onClose, title, children, maxWidth = 'md' }) {
    if (!show) return null;

    const widths = { sm: 'max-w-sm', md: 'max-w-lg', lg: 'max-w-2xl', xl: 'max-w-4xl' };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div className="fixed inset-0 bg-black/40" onClick={onClose} />
            <div className={`relative w-full ${widths[maxWidth]} bg-white rounded-xl shadow-xl p-6`}>
                <div className="flex items-center justify-between mb-4">
                    <h2 className="text-lg font-semibold text-gray-900">{title}</h2>
                    <button onClick={onClose} className="text-gray-400 hover:text-gray-600">
                        ✕
                    </button>
                </div>
                {children}
            </div>
        </div>
    );
}
