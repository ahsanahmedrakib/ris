<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ভর্তি আবেদন ফরম - {{ $admission->admission_no ?? '-' }}</title>
    {{-- Print typography, A4 pagination and html2canvas colour values,
         shared so the applicant's copy matches the admin sheet exactly. --}}
    @include('admission.sheet-head')
</head>
<body class="bg-gray-100 flex flex-col items-center py-8 gap-8">

    @include('admission.sheet-actions')

    @include('admission.sheet')

</body>

</html>
