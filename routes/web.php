<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;


Route::get('/', [ProdutoController::class, 'index']);
Route::get('/listar', [ProdutoController::class, 'listar'])->name('produto.listar');
Route::get('/detalhes', [ProdutoController::class, 'show'])->name('produto.detalhes');
Route::post('/novo', [ProdutoController::class, 'create'])->name('produto.novo');
Route::put('/atualizar', [ProdutoController::class, 'update'])->name('produto.atualizar');
Route::delete('/excluir', [ProdutoController::class, 'destroy'])->name('produto.excluir');