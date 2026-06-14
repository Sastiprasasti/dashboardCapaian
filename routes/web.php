<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use function Laravel\Ai\{agent};

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ai', function () {
    $response = agent(
        instructions: "Kamu adalah penggiat data yang luar biasa"
    )->prompt('kasih quotes 1 kalimat');
    return $response;
});

Route::get('/dashboardCapaian', function () {
    try {
        $response = agent(
            instructions: "Kamu adalah penggiat data yang luar biasa"
        )->prompt('kasih quotes 1 kalimat');

        $html = Str::markdown((string) $response);

        return <<<HTML
            <html>
                <head> 
                    <meta charset="UTF-8">
                    <style>
                        body {
                            font-family: sans-serif; 
                            max-width: 640px;
                            margin: 3rem auto;
                            padding: 0 0.5rem;
                            line-height: 1.7;
                            strong{
                                color: #1a1a1a;
                            }
                        }
                    </style>
                    
                </head>
                <body> 
                    $html
                </body>
            </html>
        HTML;
    } catch (\Laravel\Ai\Exceptions\ProviderOverloadedException $e) {
        // Jika server Gemini sibuk, berikan pesan alternatif/fallback
        return "Server AI sedang sibuk, silakan coba beberapa saat lagi.";
    } catch (\Exception $e) {
        // Menangani error umum lainnya
        return "Terjadi kesalahan: " . $e->getMessage();
    }
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
