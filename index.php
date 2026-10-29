<?php
session_start();
require_once "Robo.php";

$arquivo = "robos.json";
if (!file_exists($arquivo)) {
    file_put_contents($arquivo, "[]");
}

$robos = json_decode(file_get_contents($arquivo), true);
if (!is_array($robos)) $robos = [];

$modelos = [
    "Explorador" => [100, 20, "Investigar planetas"],
    "Engenheiro" => [120, 15, "Reparar equipamentos"],
    "Defensor" => [150, 30, "Proteger a estação"]
];

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $acao = $_POST["acao"] ?? "";

    if ($acao === "cadastrar") {
        $nome = trim($_POST["nome"] ?? "");
        $modelo = $_POST["modelo"] ?? "";
        $operador = trim($_POST["operador"] ?? "");

        if ($nome === "" || $operador === "" || !isset($modelos[$modelo])) {
            $mensagem = "Preencha todos os campos.";
        } else {
            $dadosModelo = $modelos[$modelo];
            $robo = new Robo($nome, $modelo, $dadosModelo[0], $dadosModelo[1]);
            $dados = $robo->exibirStatus();
            $dados["operador"] = $operador;
            $dados["funcao"] = $dadosModelo[2];
            $dados["id"] = uniqid();
            $robos[] = $dados;
            $_SESSION["operador"] = $operador;
            file_put_contents($arquivo, json_encode($robos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $mensagem = "Robô cadastrado com sucesso!";
        }
    }

    if ($acao === "recarregar") {
        foreach ($robos as &$dados) {
            if ($dados["id"] === ($_POST["id"] ?? "")) {
                $robo = new Robo($dados["nome"], $dados["modelo"], $dados["energia"], $dados["potencia"]);
                $robo->recarregar();
                $dados["energia"] = $robo->energia;
                $_SESSION["operador"] = $dados["operador"];
                $_SESSION["recargas"] = ($_SESSION["recargas"] ?? 0) + 1;
                $mensagem = "Robô recarregado!";
                break;
            }
        }
        unset($dados);
        file_put_contents($arquivo, json_encode($robos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

$filtro = $_GET["modelo"] ?? "Todos";
if ($filtro !== "Todos" && !isset($modelos[$filtro])) $filtro = "Todos";
$lista = array_filter($robos, function ($robo) use ($filtro) {
    return $filtro === "Todos" || $robo["modelo"] === $filtro;
});
function h($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Central de Robôs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <h1>Central de Robôs</h1>
    <p class="intro">Sistema de controle da frota espacial</p>

    <?php if (!empty($_SESSION["operador"])): ?>
        <p class="boas-vindas">Olá, <?= h($_SESSION["operador"]) ?>! Recargas nesta sessão: <?= (int)($_SESSION["recargas"] ?? 0) ?></p>
    <?php endif; ?>

    <?php if ($mensagem !== ""): ?><p class="mensagem"><?= h($mensagem) ?></p><?php endif; ?>

    <section class="painel">
        <h2>Cadastrar robô</h2>
        <form method="POST">
            <input type="hidden" name="acao" value="cadastrar">
            <label>Nome do robô</label>
            <input type="text" name="nome" required>
            <label>Modelo</label>
            <select name="modelo" required>
                <option value="">Selecione</option>
                <?php foreach ($modelos as $nomeModelo => $dados): ?>
                    <option value="<?= h($nomeModelo) ?>"><?= h($nomeModelo) ?></option>
                <?php endforeach; ?>
            </select>
            <label>Nome do operador</label>
            <input type="text" name="operador" value="<?= h($_SESSION["operador"] ?? "") ?>" required>
            <button type="submit">Cadastrar</button>
        </form>
    </section>

    <section class="painel">
        <h2>Frota cadastrada</h2>
        <form method="GET" class="filtro">
            <label>Filtrar por modelo</label>
            <select name="modelo">
                <option value="Todos">Todos os modelos</option>
                <?php foreach ($modelos as $nomeModelo => $dados): ?>
                    <option value="<?= h($nomeModelo) ?>" <?= $filtro === $nomeModelo ? "selected" : "" ?>><?= h($nomeModelo) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filtrar</button>
        </form>

        <?php if (empty($lista)): ?>
            <p>Nenhum robô cadastrado.</p>
        <?php endif; ?>

        <?php foreach ($lista as $robo): ?>
            <article class="robo <?= strtolower(h($robo["modelo"])) ?>">
                <h3><?= h($robo["nome"]) ?> <small>(<?= h($robo["modelo"]) ?>)</small></h3>
                <p>Função: <?= h($robo["funcao"]) ?></p>
                <p>Energia: <?= (int)$robo["energia"] ?>/200</p>
                <p>Potência: <?= (int)$robo["potencia"] ?></p>
                <p>Operador: <?= h($robo["operador"]) ?></p>
                <form method="POST">
                    <input type="hidden" name="acao" value="recarregar">
                    <input type="hidden" name="id" value="<?= h($robo["id"]) ?>">
                    <button type="submit">Recarregar +10</button>
                </form>
            </article>
        <?php endforeach; ?>
    </section>
    <p class="rodape">Central de Robôs - PHP e JSON</p>
</main>
</body>
</html>