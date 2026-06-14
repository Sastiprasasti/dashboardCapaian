<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div
class="bg-white dark:bg-slate-800
       px-6 py-4 shadow-sm
       flex justify-between items-center"
>

<div>

<h1 class="text-3xl font-bold">
    Capaian Kinerja SE2026
</h1>

<p class="text-slate-500">
    BPS Kabupaten Garut
</p>

</div>

<div class="flex gap-3">

<button
    @click="sidebar=!sidebar"
    class="px-4 py-2 rounded-xl bg-slate-200"
>
☰
</button>

<button
    @click="dark=!dark"
    class="px-4 py-2 rounded-xl bg-slate-200"
>
🌙
</button>

<select
    id="kecamatan"
    class="rounded-xl border px-4 py-2"
>

<option value="">
Semua Kecamatan
</option>

@foreach($kecamatan as $kec)

<option value="{{ $kec }}">
    {{ $kec }}
</option>

@endforeach

</select>

</div>

</div>
</body>
</html>