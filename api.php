<?php
// Definindo o cabeçalho para indicar que é uma API Rest e permitir acesso de qualquer origem (CORS)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Verifica o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Função para retornar todos os usuários em formato JSON
function get_users() {
    $users_file = 'users.json';
    $users_data = json_decode(file_get_contents($users_file), true);

    // Verifica se houve erro na leitura do arquivo JSON
    if ($users_data === null) {
        http_response_code(500); // Erro interno do servidor
        echo json_encode(['error' => 'Erro ao ler dados de usuários']);
    } else {
        echo json_encode($users_data);
    }
}

// Função para adicionar um novo usuário ao arquivo users.json
function add_user() {
    // Verifica se a requisição é do tipo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Método não permitido
        echo json_encode(['error' => 'Método não permitido']);
        return;
    }

    // Recebe os dados da requisição POST
    $data = $_POST;

    // Verifica se o nome do novo usuário foi enviado na requisição
    if (isset($data['name'])) {
        $users_file = 'users.json';

        // Carrega os dados existentes do arquivo JSON
        $users_data = json_decode(file_get_contents($users_file), true);

        // Verifica se a leitura do arquivo JSON foi bem-sucedida
        if ($users_data === null) {
            $users_data = []; // Inicializa como um array vazio se o arquivo estiver vazio ou não existir
        }

        // Cria o novo usuário com um ID único
        $new_user = ['id' => uniqid(), 'name' => $data['name']];

        // Adiciona o novo usuário aos dados existentes
        $users_data[] = $new_user;

        // Salva os dados atualizados no arquivo JSON
        file_put_contents($users_file, json_encode($users_data));

        // Redireciona para a página index.html
        header("Location: index.html");

    } else {
        http_response_code(400); // Requisição inválida
        echo json_encode(['error' => 'Nome do usuário não fornecido']);
    }
}

// Função principal para processar a requisição
switch ($method) {
    case 'GET':
        get_users();
        break;
    case 'POST':
        add_user();
        break;
    default:
        http_response_code(405); // Método não permitido
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
?>
