<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Get all produtos

$app->get('/api/produtos', function(Request $request, Response $response){
	$sql = "SELECT * FROM produtos";

	try{

		$db = new db();

		$db = $db->connect();

		$stmt = $db->query($sql);
		$produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
		$DB = null;
		echo json_encode($produtos);

	} catch(PDOException $e){
		 echo '{"error": {"text": '.$e->getMessage().'}}';
	}


});

// Get by categoria

$app->get('/api/produtos/{categoria}', function(Request $request, Response $response){

	$categoria = $request->getAttribute('categoria');
	$sql = "SELECT * FROM produtos WHERE categoria = '$categoria'";

	try{

		$db = new db();

		$db = $db->connect();

		$stmt = $db->query($sql);
		$produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
		$DB = null;
		echo json_encode($produtos);

	} catch(PDOException $e){
		 echo '{"error": {"text": '.$e->getMessage().'}}';
	}


});



// Adiciona Produto

$app->post('/api/produtos/add', function(Request $request, Response $response){
	$nome = $request->getParam('nome');
	$categoria = $request->getParam('categoria');
	$preco = $request->getParam('preco');
	$descricao = $request->getParam('descricao');

	$sql = "INSERT INTO produtos (nome, categoria, preco, descricao) VALUES (:nome, :categoria, :preco, :descricao)";

	try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':categoria',  $categoria);
        $stmt->bindParam(':preco',      $preco);
        $stmt->bindParam(':descricao',      $descricao);
        
        $stmt->execute();
        echo '{"notice": {"text": "Produto adicionado"}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

});

// Editar Produto
$app->put('/api/produtos/update/{cod}', function(Request $request, Response $response){
    $cod = $request->getAttribute('cod');
    $nome = $request->getParam('nome');
    $categoria = $request->getParam('categoria');
    $preco = $request->getParam('preco');
    $descricao = $request->getParam('descricao');
   
    $sql = "UPDATE produtos SET
				nome 	= :nome,
				categoria 	= :categoria,
                preco		= :preco,
                descricao = :descricao
			WHERE cod = $cod";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':categoria',  $categoria);
        $stmt->bindParam(':preco',      $preco);
        $stmt->bindParam(':descricao',      $descricao);
        
        $stmt->execute();
        echo '{"notice": {"text": "Produto Atualizado"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Delete Produto
$app->delete('/api/produtos/delete/{cod}', function(Request $request, Response $response){
    $cod = $request->getAttribute('cod');
    $sql = "DELETE FROM produtos WHERE cod = $cod";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Produto Removido"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

