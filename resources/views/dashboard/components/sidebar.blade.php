<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <aside
    :class="sidebar ? 'w-64' : 'w-20'"
    class="transition-all duration-300
           bg-slate-950 text-white"
>

<div class="p-5">

    <div class="flex items-center gap-3">

        <img src="{{ asset('img/bps.png') }}"
             class="w-10">

        <span x-show="sidebar"
              class="font-bold">
            SE2026
        </span>

    </div>

</div>

<nav class="mt-8">

    <a href="#"
       class="flex items-center gap-3 px-5 py-4 hover:bg-orange-500">

        <i class="fas fa-chart-line"></i>

        <span x-show="sidebar">
            Dashboard
        </span>

    </a>

</nav>

</aside>
</body>
</html>