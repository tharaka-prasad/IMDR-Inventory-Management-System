/**
 * Laravel serializes date/datetime casts as full ISO-8601 strings
 * (e.g. "2026-09-29T00:00:00.000000Z"). Every table column that shows a
 * date should run the value through this helper instead of printing it raw.
 */
export function formatDate(value, { withTime = false } = {}) {
    if (!value) return '-';

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value; // not a parseable date, show as-is

    const options = withTime
        ? { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
        : { year: 'numeric', month: 'short', day: 'numeric' };

    return date.toLocaleString('en-GB', options);
}
