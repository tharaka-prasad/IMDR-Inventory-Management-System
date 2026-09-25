<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        .meta { color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px 7px; text-align: left; }
        th { background-color: #2563eb; color: #fff; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .footer { margin-top: 16px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>{{ $instituteName }} - {{ $title }}</h1>
    <div class="meta">
        Generated: {{ now()->format('Y-m-d H:i') }}
        @if(!empty($dateRange))
            &nbsp;|&nbsp; Period: {{ $dateRange }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headings) }}">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">IMDR Inventory Management System &mdash; Confidential</div>
</body>
</html>
