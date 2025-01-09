<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        footer {
            width: 100%;
            text-align: center;
            font-size: 12px;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<footer>
    {{ $requisition_id }} - Page @pageNumber of @totalPages
</footer>


</html>
