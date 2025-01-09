<!DOCTYPE html>
<html lang="en">

<head>
    <title>Material Requisition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            margin: 10mm;
            /* Adjust the margins for your PDF */
        }

        /* Ensuring content that exceeds the page size moves to the next page */
        .page-break {
            page-break-before: always;
        }

        /* Allow tables to break between pages if needed */
        table {
            page-break-inside: auto;
        }

        /* Ensure footer always stays at the bottom of the page */
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            background-color: #f1f1f1;
            padding: 5px 0;
        }

        /* Prevent elements from overlapping the footer */
        .content {
            min-height: calc(100vh - 50px);
            /* Reserve space for the footer */
        }
    </style>
</head>

<body class="font-sans text-sm   ">
    <div class="flex justify-between items-center mb-5 border-b border-gray-300 p-2">
        <img src="{{ public_path('Superior New logo.png') }}" alt="Logo" class="w-36 h-auto">
        <div class="text-right">
            <h1 class="text-lg font-medium">Material requisition</h1>
            <h1 class="text-md ">{{ $requisition_id }}</h1>
        </div>
    </div>
    <div>
        <table class="w-full border border-black">
            <tr>
                <td class="text-left border border-black w-1/2 bg-black text-white font-bold px-2">Date Required</td>
                <td class="border border-black w-1/2 px-2"> {{ \Carbon\Carbon::parse($date_required)->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="mt-5">
        <table class="w-full border border-black">
            <tr>
                <td class="text-left border border-black bg-black text-white font-bold px-2" colspan="4">Site details
                </td>
            </tr>
            <tr>
                <td class="text-left border border-black w-1/4 px-2">Project</td>
                <td class="text-left border border-black w-1/4 px-2"> {{ $project_id }}</td>
                <td class="text-left border border-black w-1/4 px-2">Site reference</td>
                <td class="text-left border border-black w-1/4 px-2"> {{ $site_ref }}</td>
            </tr>
        </table>
    </div>
    <div class="mt-5">
        <table class="w-full border border-black">
            <tr>
                <td class="text-left border border-black bg-black text-white font-bold px-2" colspan="2">Order
                    details</td>
            </tr>
            <tr>
                <td class="text-left border border-black w-1/4 px-2">Supplier</td>
                <td class="text-left border border-black w-3/4 px-2"> {{ $supplier }}</td>
            </tr>
            <tr>
                <td class="text-left border border-black w-1/4 px-2">Delivery contact</td>
                <td class="text-left border border-black w-3/4 px-2"> {{ $delivery_contact }}</td>
            </tr>
            <tr>
                <td class="text-left border border-black w-1/4 px-2">Pickup by</td>
                <td class="text-left border border-black w-3/4 px-2"> {{ $pickup_by }}</td>
            </tr>
            <tr>
                <td class="text-left border border-black w-1/4 px-2">Requested by</td>
                <td class="text-left border border-black w-3/4 px-2"> {{ $requested_by }}</td>
            </tr>
        </table>
    </div>

    <div class="mt-5">
        <table class="w-full border border-black">
            <tr>
                <td class="text-left border border-black bg-black text-white  w-full font-bold px-2" colspan="2">
                    Delivery
                    details
                </td>
            </tr>
            <tr>
                <td class="text-left border border-black w-1/4 px-2">Delivery to</td>
                <td class="text-left border border-black w-3/4 px-2"> {{ $delivery_to }}</td>
            </tr>
            <tr>
                <td class="text-left border border-black w-1/4 px-2">Notes</td>
                <td class="text-left border border-black w-3/4 px-2"> {!! $notes !!}</td>
            </tr>
        </table>
    </div>


    <table class="w-full border-collapse border border-black mb-10 mt-5">
        <thead>
            <tr>
                <td colspan="3" class="border border-black px-2 bg-black text-white font-bold">
                    Requested Items
                </td>
            </tr>
            <tr>
                <th class="border border-black px-2 py-1 text-left">Item Code</th>
                <th class="border border-black px-2 py-1 text-left">Description</th>
                <th class="border border-black px-2 py-1 text-left">Quantity</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($req_lines as $line)
                <tr>
                    <td class="border border-black px-2 py-1">{{ $line['item_code'] }}</td>
                    <td class="border border-black px-2 py-1">{{ $line['description'] }}</td>
                    <td class="border border-black px-2 py-1">{{ $line['qty'] }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
