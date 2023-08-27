<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
</head>
<body>

    <form name="novoProduto" class="estilo">
        @csrf
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>

        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" id="descricao">

        <label for="preco">Preço:</label>
        <input type="number" step="any" name="preco" id="preco">

        <label for="quantidade">Quantidade:</label>
        <input type="number" name="quantidade" id="quantidade">

        <input type="submit" value="Enviar">
    </form>
    <div class="estilo">
        <label for="nome">Pesquisar por nome:</label>
        <input type="text" name="nome" id="pesquisa-nome">
    </div>
    <table id="tabela-produtos">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div id="exclusaoProduto">
        <p>Deseja confirmar a exclusão</p>
        <button name="confirmarExclusao">Confirmar</button>
        <button name="cancelarExclusao">Cancelar</button>
    </div>

    <div id="formularioContainer">
        <form name="editarProduto">
            @csrf
            <label for="nomeEdit">Nome:</label>
            <input type="text" name="nomeEdit" id="nomeEdit" required>
    
            <label for="descricaoEdit">Descrição:</label>
            <input type="text" name="descricaoEdit" id="descricaoEdit">
    
            <label for="precoEdit">Preço:</label>
            <input type="number" step="any" name="precoEdit" id="precoEdit">
    
            <label for="quantidadeEdit">Quantidade:</label>
            <input type="number" name="quantidadeEdit" id="quantidadeEdit">

            <input type="hidden" name="idEdit">
    
            <input type="submit" name="alterarEdicao" value="Alterar">
            <input type="submit" name="cancelar" value="Cancelar">
        </form>
      </div>

      <div id="listarProdutoUnico">
      <table id="listarProduto">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        
    </table>
    <button name="fecharVisualizacao">Fechar Visualização</button>
</div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

    <script>
        function carregarData(){
            $.ajax({
                url:"{{ route('produto.listar') }}",
                type: "get",
                dataType: "json",
                success: function (data) {
                    carregarProdutos(data);
                },
                error: function(error){
                    console.error(`Erro na requisição ${error}`)
                }
            });
        };
    
        function editarProduto(dados) {

            $.ajax({
                url: "{{ route('produto.detalhes') }}",
                type: "get",
                data: dados,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if(dados.value == 'visualizar'){
                        visualizar(data);
                    }else if(dados.value == 'editar'){
                        editar(data);
                    }
                },
                error: function (error) {
                    console.error(`Erro na requisição ${error}`);
                }
            });
        };

        function visualizar(data){
            var tabela = $('#listarProduto tbody');
            tabela.empty();
            var produto = data[0];

                var row = $('<tr>');
                row.append($('<td id="nome-produto">').text(produto.nome));
                row.append($('<td>').text(produto.descricao));
                row.append($('<td>').text(produto.preco));
                row.append($('<td>').text(produto.quantidade));
                tabela.append(row);

        };

        function carregarProdutos(data){
            var tabela = $('#tabela-produtos tbody');
           
            tabela.empty();

            $.each(data, function(produtos, produto){
                var row = $('<tr>');
                row.append($('<td id="nome-produto">').text(produto.nome));
                row.append($('<td>').text(produto.descricao));
                row.append($('<td>').text(produto.preco));
                row.append($('<td>').text(produto.quantidade));
                row.append($('<button type="submit" name="editar">').text('Editar').attr('value', produto.id))
                row.append($('<button type="submit" name="visualizar">').text('Visualizar').attr('value', produto.id))
                row.append($('<button type="submit" name="excluir">').text('Excluir').attr('value', produto.id))
                tabela.append(row);
            })
        };

        function editar(dados){

            $("#formularioContainer").find("input#nomeEdit").val(dados[0].nome);
            $("#formularioContainer").find("input#descricaoEdit").val(dados[0].descricao);
            $("#formularioContainer").find("input#precoEdit").val(dados[0].preco);
            $("#formularioContainer").find("input#quantidadeEdit").val(dados[0].quantidade);
            $("#formularioContainer").find("input#idEdit").val(dados[0].id);

            $(document).on('click', 'input[name="alterarEdicao"]', function(event){
                $('form[name="editarProduto"]').submit(function(event){
                event.preventDefault();

                var dadosEdit = {
                    "nome": $(this).find('input#nomeEdit').val(),
                    "descricao": $(this).find('input#descricaoEdit').val(),
                    "preco": $(this).find('input#precoEdit').val(),
                    "quantidade": $(this).find('input#quantidadeEdit').val(),
                    "id": dados[0].id,
                }
                $.ajax({
                    url: "{{ route('produto.atualizar') }}",
                    type: "put",
                    data: dadosEdit,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        carregarData();
                        $("#formularioContainer").hide();
                        alert('Produto atualizado com sucesso!')
                    },
                    error: function (error) {
                        console.error(`Erro na requisição ${error}`);
                    }
                });

            });
            })
        };

        function cancelarEdicao(){
            $("#formularioContainer").find("input#nome").val("")
            $("#formularioContainer").find("input#descricao").val("")
            $("#formularioContainer").find("input#preco").val("")
            $("#formularioContainer").find("input#quantidade").val("")
                    
            $("#formularioContainer").hide();
        };

        function excluirProduto(dados){
            $("#exclusaoProduto").hide();
            $.ajax({
                url: "{{ route('produto.excluir') }}",
                type: "delete",
                data: dados,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    alert('Produto excluido com sucesso!');
                    $("#exclusaoProduto").hide();
                    carregarData()
                },
                error: function (error) {
                    console.error(`Erro na requisição ${error}`);
                }
            });
        };

        $(document).ready(function(){
            
            $('form[name="novoProduto"]').submit(function(event){
                event.preventDefault();
                cancelarEdicao();
                var nome = $(this).find('input#nome').val();
                var descricao = $(this).find('input#descricao').val();
                var preco = $(this).find('input#preco').val();
                var quantidade = $(this).find('input#quantidade').val();

                $.ajax({
                    url:"{{ route('produto.novo') }}",
                    type: "post",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response){

                        if(response){
                        $('input#nome').val("")
                        $('input#descricao').val("")
                        $('input#preco').val("")
                        $('input#quantidade').val("")
                        }
                        carregarData();
                        alert(`O produto ${nome} foi registrado com sucesso!`)
                    },
                    error: function(jqXHR){
                        if(jqXHR.status == 422){
                            var mensagemErro = 'Ocorreu um erro de validação, verifique os dados.';
                        }else{
                            var mensagemErro = 'Ocorreu um erro ao processar sua solicitação';
                        }
                        alert(mensagemErro);
                        //console.log(jqXHR)
                    }
                });
            });

            $('input[name="cancelar"]').click(function(event) {
                    event.preventDefault();
                    cancelarEdicao();
                });



            $("#pesquisa-nome").on("keyup", function() {

                var value = $(this).val().toLowerCase();

                $("#tabela-produtos tbody tr").each(function() {
                var campoPesquisa = $(this).find("#nome-produto").text().toLowerCase();
                $(this).toggle(campoPesquisa.indexOf(value) > -1);
                });

            });

            carregarData();
        });

        $(document).on('click', 'button[name="editar"]', function() {
            $("#listarProdutoUnico").hide();
            var dados = {
                "id":  $(this).attr('value'),
                "value": $(this).attr('name'),
            }
            $("#formularioContainer").slideDown();
            editarProduto(dados);
        });

        $(document).on('click', 'button[name="visualizar"]', function() {

            $("#listarProdutoUnico").slideDown();

            var dados = {
                "id":  $(this).attr('value'),
                "value": $(this).attr('name'),
            }
            editarProduto(dados);
        });

        $(document).on('click', 'button[name="excluir"]', function() {
            $("#listarProdutoUnico").hide();
            $("#exclusaoProduto").slideDown();

            cancelarEdicao();
            var dados = {
                "id":  $(this).attr('value'),
            }
            if($(document).on('click', 'button[name="confirmarExclusao"]',function(){
                excluirProduto(dados);
            }));

            if($(document).on('click', 'button[name="cancelarExclusao"]',function(){
                $("#exclusaoProduto").hide();
            }));

        });

        $(document).on('click', 'button[name="fecharVisualizacao"]', function(){
            $("#listarProdutoUnico").hide();
        })

    </script>

    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 2% auto;
        }

        th, td {
            border: 2px solid black;
            padding-left: 8px;
            text-align: center;
        }

        button {
            align-content: center;
            padding: 5px;
        }

        th {
            background-color: #f2f2f2;
        }

        #confirmarExclusao{
            background-color:green;
            font-weight: bold;
            color: white;
        }

        #cancelarExclusao{
            background-color:red;
            font-weight: bold;
            color: white;
        }

        .estilo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #exclusaoProduto {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: none;
        }

        #formularioContainer{
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: none;
        }

        #listarProdutoUnico{
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: none;
        }

        label, p{
            font-weight: bold;
        }


        input[type="text, number"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease-in-out;
        }


        input[type="text, number"]:focus {
            border-color: #3498db;
            outline: none;
        }
    </style>

</body>
</html>

