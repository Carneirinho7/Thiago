<?php
class Robo {
    public $nome;
    public $modelo;
    public $energia;
    public $potencia;

    public function __construct($nome, $modelo, $energia, $potencia) {
        $this->nome = $nome;
        $this->modelo = $modelo;
        $this->energia = $energia;
        $this->potencia = $potencia;
    }

    public function exibirStatus() {
        return [
            "nome" => $this->nome,
            "modelo" => $this->modelo,
            "energia" => $this->energia,
            "potencia" => $this->potencia
        ];
    }

    public function recarregar() {
        $this->energia = min(200, $this->energia + 10);
    }
}
?>