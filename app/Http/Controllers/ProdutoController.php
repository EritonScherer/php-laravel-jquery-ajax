<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{

    public function listar()
    {
        $produtos = Produto::select('id','nome','descricao','preco','quantidade')
        ->get();
        
        return $produtos;
    }

    public function index()
    {
        return view('home');
    }

    public function create(Request $request)
    {
        $validData = $request->validate([
            'nome' => 'required',
            'descricao' => 'nullable',
            'preco' => 'nullable|numeric|min:0',
            'quantidade' => 'nullable|numeric|integer|min:0',
        ]);
        $produto = new Produto();
        
        $produto->fill($validData);
        $produto->save();

        return $validData;
    }

    public function show(Request $request)
    { 
        $dados = $request->query();

        $produto = Produto::select('id','nome','descricao','preco','quantidade')
        ->when($dados['id'], function($filtrarId, $id){
            return $filtrarId->where('id', $id);})
        ->get();

        return $produto; 
    }

    public function update(Request $request)
    {
        $validData = $request->validate([
            'nome' => 'required',
            'descricao' => 'nullable',
            'preco' => 'nullable|numeric|min:0',
            'quantidade' => 'nullable|numeric|integer|min:0',
        ]);
        try{
            $id = $request->input('id');
            
            $produto = Produto::find($id);

            $produto->nome = $validData['nome'];
            $produto->descricao = $validData['descricao'];
            $produto->preco = $validData['preco'];
            $produto->quantidade = $validData['quantidade'];

            $produto->save();

            return $produto;
        }catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar o produto',], 500);
        }

    }

    public function destroy(Request $request)
    {  
        try {
            $id = $request->input('id');
            $produto = Produto::find($id)->delete();
            
            return $produto;

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir o produto',], 500);
        }
    }
}
