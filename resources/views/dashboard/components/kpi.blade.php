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
class="grid grid-cols-1
       md:grid-cols-2
       xl:grid-cols-4
       gap-6"
>

<div
class="rounded-3xl p-6
bg-gradient-to-r
from-orange-500 to-orange-600
text-white"
>
<h4>Total Assignment</h4>
<h1 class="text-4xl font-bold">
{{ total }}
</h1>
</div>

<div
class="rounded-3xl p-6
bg-gradient-to-r
from-green-500 to-green-600
text-white"
>
<h4>Submit</h4>
<h1>{{ submit }}</h1>
</div>

<div
class="rounded-3xl p-6
bg-gradient-to-r
from-red-500 to-red-600
text-white"
>
<h4>Open</h4>
<h1>{{ open }}</h1>
</div>

<div
class="rounded-3xl p-6
bg-gradient-to-r
from-blue-500 to-blue-600
text-white"
>
<h4>Progress</h4>
<h1>{{ progress }}%</h1>
</div>

</div>
</body>
</html>