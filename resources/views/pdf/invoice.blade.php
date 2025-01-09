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
            max-width: 150px;
            /* Adjust the logo size */
            height: auto;
            /* Maintain aspect ratio */
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }


        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 1px solid gray;

        }

        .header-table-items {
            width: 50%;
            border: none;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 50%; border: none;"> <img src="{{ public_path('Superior New logo.png') }}" alt="Logo"
                    class="logo"></td>
            <td style="text-align: right; border: none;">
                <p style="font-size: 20px; font-weight: bold; margin-bottom: 10px;">Material requisition</p>
                <p style="font-size: 16px; color: gray;">REQ-0001</p>
            </td>
        </tr>
    </table>
    <div>
        <table class="header-table">
            <tr>
                <td style="width: 50%; background: black; color: white;  font-weight: bold;">Date Required
                </td>
                <td> {{ \Carbon\Carbon::parse($date_required)->format('d/m/Y') }}
                </td>
            </tr>
        </table>
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
    <table class="table-items">
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Description</th>
                <th>Quantity</th>
                {{-- <th>Cost (ea)</th>
                <th>Total</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($req_lines as $line)
                <tr>
                    <td>{{ $line['item_code'] }}</td>
                    <td>{{ $line['description'] }}</td>
                    <td>{{ $line['qty'] }}</td>
                    {{-- <td>
                   {{$line['cost']}}
                </td>
                <td>{{$line['cost'] * $line['qty']}}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
