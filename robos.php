<?php

class robo {
    public $nome;
    public $modelo;
    public $operador;
    public $energia;
    public $potencia;

public function __construct($nome, $modelo, $energia, $potencia,$operador){
    $this->nome = $nome;
    $this->modelo = $modelo;
    $this->operador = $operador;
    $this->energia = $energia;
    $this->potencia = $potencia;
}

public function recarregar(){
    $this->energia += 10;
    if ($this->energia > 200){
        $this->energia > 200;
    }
}

}




















?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>