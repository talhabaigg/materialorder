<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>Material Requisition</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: left;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px; /* Adjust the logo size */
            height: auto; /* Maintain aspect ratio */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo1.jpg') }}" alt="Logo" class="logo">
        <h1>Material Requisition</h1>
    </div>

    <p><strong>Date Required:</strong> {{ \Carbon\Carbon::parse($date_required)->format('d/m/Y') }}</p>
    <p><strong>Supplier:</strong> {{ $supplier }}</p>
    <p><strong>Project:</strong> {{ $project_id }}</p>
    <p><strong>Site Reference:</strong> {{ $site_ref }}</p>
    <p><strong>Delivery Contact:</strong> {{ $delivery_contact }}</p>
    <p><strong>Pickup By:</strong> {{ $pickup_by }}</p>
    <p><strong>Requested By:</strong> {{ $requested_by }}</p>
    <p><strong>Delivery To:</strong> {{ $delivery_to }}</p>
    <p><strong>Notes:</strong> {!! $notes !!}</p> <!-- Rendered HTML from Markdown -->

    <h2>Requested Items</h2>
    <table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Cost (ea)</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($req_lines as $line)
            <tr>
                <td>{{ $line['item_code'] }}</td>
                <td>{{ $line['description'] }}</td>
                <td>{{ $line['qty'] }}</td>
                <td>
                    {{ number_format((float) $line['cost'], 2) }}
                </td>
                <td>
                    {{ number_format($line['cost'] * $line['qty'], 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
