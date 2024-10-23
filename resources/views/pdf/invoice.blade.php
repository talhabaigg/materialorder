

<html lang="en">
<head>
    <title>Requisition</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="px-8 py-8  mx-auto">
    <div class="flex items-center">
        <div class="text-blue-500 font-semibold text-3xl">Superior Group</div>
        
    </div>
    <div class="flex items-center justify-between mb-8">
        
        <div class="text-gray-700 text-xs">
            <div class="font-bold uppercase text-lg  mb-4">Requisition</div>
            <p><strong>Date Required:</strong> {{ $date_required }}</p>
            <p><strong>Supplier:</strong> {{ $supplier }}</p>
            <p><strong>Project ID:</strong> {{ $project_id }}</p>
            <p><strong>Site Reference:</strong> {{ $site_ref }}</p>
            <p><strong>Delivery Contact:</strong> {{ $delivery_contact }}</p>
            <p><strong>Pickup By:</strong> {{ $pickup_by }}</p>
            <p><strong>Requested By:</strong> {{ $requested_by }}</p>
            {{-- <p><strong>Delivery To:</strong> {{ $delivery_to }}</p> --}}
            <p><strong>Notes:</strong> {{ $notes }}</p>
        </div>
       
    </div>
    <div class="border-b-2 border-gray-300 pb-8 mb-8">
        <h2 class="text-lg font-bold mb-4">Deliver To:</h2>
        <div class="text-xs text-gray-700 mb-2">{{ $delivery_contact }}</div>
        <div class="text-xs text-gray-700 mb-2"> {{ $delivery_to }}</div>
        
    </div>
    <table class="w-full text-left mb-8 text-xs rounded-sm overflow-hidden px-8">
        <thead class="bg-gray-200 ">
        <tr >
            <th class="text-gray-700 text-xs font-bold uppercase py-2 px-2">Item Code</th>
            <th class="text-gray-700 text-xs font-bold uppercase py-2 px-2">Description</th>
            <th class="text-gray-700  text-xs font-bold uppercase py-2">Quantity</th>
            <th class="text-gray-700 text-xs font-bold uppercase py-2">Cost</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($req_lines as $line)
            <tr>
                <td class="border-b text-xs border-black p-2">{{ $line['item_code'] }}</td>
                <td class="border-b text-xs border-black p-2">{{ $line['description'] }}</td>
                <td class="border-b text-xs border-black p-2">{{ $line['qty'] }}</td>
                <td class="border-b text-xs border-black p-2">${{ $line['cost'] ?? 'NA' }}</td>
            </tr>
            @endforeach
        
        </tbody>
    </table>

</div>

</body>
</html>