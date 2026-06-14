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
    x-data="{
        sidebar:true,
        dark:false
    }"
    :class="dark ? 'dark' : ''"
>

<div class="flex h-screen bg-slate-100 dark:bg-slate-900">

    <!-- Sidebar -->
    @include('dashboard.components.sidebar')

    <!-- Main -->
    <div class="flex-1 overflow-auto">

        @include('dashboard.components.header')

        <div class="p-6">

            <!-- KPI -->
            <div id="kpiContainer"></div>

            <!-- Charts -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">

                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow">
                    <canvas id="petugasChart"></canvas>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow">
                    <canvas id="progressChart"></canvas>
                </div>

            </div>

            <!-- Table -->
            <div
                class="bg-white dark:bg-slate-800 mt-6 rounded-3xl p-6 shadow"
            >
                <div id="rankingContainer"></div>
            </div>

        </div>

    </div>

</div>

</div>
</body>
</html>