import Modal from './Modal';

export default function ConfirmDelete({ show, onClose, onConfirm, title = 'Delete this record?', description, processing }) {
    return (
        <Modal show={show} onClose={onClose} title={title} maxWidth="sm">
            <p className="text-sm text-gray-600 mb-6">
                {description || 'This action moves the record to trash. It can be restored later by a Super Admin if needed.'}
            </p>
            <div className="flex justify-end gap-3">
                <button
                    onClick={onClose}
                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                >
                    Cancel
                </button>
                <button
                    onClick={onConfirm}
                    disabled={processing}
                    className="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50"
                >
                    {processing ? 'Deleting...' : 'Delete'}
                </button>
            </div>
        </Modal>
    );
}
