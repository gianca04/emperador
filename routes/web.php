<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkReportPdfController;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
Route::redirect('/', '/dashboard');

Livewire::setScriptRoute(function ($handle) {
    return Route::get('/emperador/public/livewire/livewire.js', $handle);
});

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/emperador/public/livewire/update', $handle);
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
});


Route::get('/crear-symlink', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    if (file_exists($link)) {
        return '⚠️ Ya existe un enlace o carpeta llamado "storage" en public.';
    }

    if (symlink($target, $link)) {
        return '✅ Enlace simbólico creado correctamente.';
    } else {
        return '❌ No se pudo crear el enlace simbólico.';
    }
});
