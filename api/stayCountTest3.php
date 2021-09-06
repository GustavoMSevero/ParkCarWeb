<?php
// EXEMPLO WALDIR
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("timeToMinutes.php");

// function calc($veiculoValor, $valorHora) {
//     return $veiculoValor + $valorHora;
// }

// $veiculo = ''; // P=pequeno - M=moto - G=grande
// $veiculoValor;

// switch ($veiculo) { 
//     case 'G':
//         $veiculoValor = 2.50;
//         break;
//     case 'M':
//         $veiculoValor = 0.50;
//         break;
//     case 'P':
//         $veiculoValor = 1.50;
//         break;
//     default:
//         $veiculoValor = 0.0;
//         break;
// }

$entrada = date("2021-08-27 16:00:00");
$entrada_DateTime = new DateTime($entrada);


$saida = date("2021-08-27 18:00:00");
$saida_DateTime = new DateTime($saida);

$difference = $entrada_DateTime ->diff($saida_DateTime );
$return_time = $difference ->format('%H:%I:%S');

$entradaEmMinutos = dateAndTimeToNumber($entrada);
$saidaEmMinutos = dateAndTimeToNumber($saida);

$permanencia = ($entradaEmMinutos - $saidaEmMinutos) * -1;

if ($tolerancia != NULL) {
    // echo 'Tem tolerancia'.'<br>';

} else {
    // echo 'Não tem tolerancia'.'<br>';

}

// $diaDaSemanda = 'Segunda';
// $diaDeTolerancia = 'Segunda';
// $tempoDeToleranciaEmDiaDeTolerancia = 60;
// $tempoDeToleranciaDemaisDias = 30;

// if ($diaDaSemanda == $diaDeTolerancia) {
//     $tolerancia = $tempoDeToleranciaEmDiaDeTolerancia;
//     echo 'dia da semana '.$diaDaSemanda.'<br>';
//     echo 'tolerancia '.$tolerancia.'<br>';
// } else {
//     $tolerancia = $tempoDeToleranciaDemaisDias;
//     echo 'dia da semana '.$diaDaSemanda.'<br>';
//     echo 'tolerancia '.$tolerancia.'<br>';
// }


$valorHora = 7.00;
$tolerancia = 30;

$estacionamento_valor_dobrado_e_com_desconto = true;
// Estacionamento Carlos Gomes
// $meia_hora = 6.00;
// $uma_hora = 12.00;
// $meio_hora_adi = 5.00;
// $uma_hora_adi = 10.00;

$estacionamento_valor_dobrado = false;
$meia_hora = 5.00;
$uma_hora = 10.00;
$meio_hora_adi = 0.00;
$uma_hora_adi = 0.00;


echo 'entrada em minutos '.$entradaEmMinutos.'<br>';
echo 'saida em minutos '.$saidaEmMinutos.'<br>';
echo 'permanencia '.$permanencia.'<br>';
echo 'tolerancia '.$tolerancia.'<br>';
// echo 'veiculo '.$veiculoValor.' valor hora '.$valorHora.'<br>';

// if ($permanencia <= $tolerancia) {
//     echo 'Isento'.'<br>';
// } elseif ($permanencia > $tolerancia && $permanencia <= $tolerancia + 30) {
//     echo 'Pagar 30 min'.'<br>';
//     // echo 'Pagar R$ '.calc($veiculoValor, $valorHora/2);
//     echo 'Pagar R$ '.$meia_hora;
// } elseif ($permanencia > $tolerancia + 30 && $permanencia <= $tolerancia + 60) {
//     echo 'Pagar 1 hora'.'<br>';
//     // echo 'Paga R$ '.(cal($veiculoValor, $valorHora));
//     echo 'Paga R$ '.$uma_hora;
// } elseif ($permanencia > $tolerancia + 60 && $permanencia <= $tolerancia + 90) {
//     echo 'Pagar 1 hora'.'<br>';
//     // echo 'Paga R$ '.cal($veiculoValor, $valorHora+($valorHora/2));
//     echo 'Paga R$ '.$uma_hora+($meio_hora_adi);
// } elseif ($permanencia > 90 && $permanencia <= 120) {
//     echo 'Pagar 2:00 horas'.'<br>';
//     echo 'Paga R$ '.$uma_hora+($uma_hora_adi);
// } elseif ($permanencia > 120 && $permanencia <= 150) {
//     echo 'Pagar 2:30 horas '.'<br>';
//     echo 'Paga R$ '.$uma_hora+($uma_hora_adi + $meio_hora_adi);
// } elseif ($permanencia > 150 && $permanencia <= 180) {
//     echo 'Pagar 3:00 horas '.'<br>';
// }

if ($permanencia <= 30) {
    echo 'Pagar 30 min'.'<br>';
    // echo 'Pagar R$ '.calc($veiculoValor, $valorHora/2);
    echo 'Pagar R$ '.$meia_hora;
} elseif ($permanencia > 30 && $permanencia <= 60) {
    echo 'Pagar 1 hora'.'<br>';
    // echo 'Paga R$ '.(cal($veiculoValor, $valorHora));
    echo 'Paga R$ '.$uma_hora;
} elseif ($permanencia > 60 && $permanencia <= 90) {
    echo 'Pagar 1 hora'.'<br>';
    // echo 'Paga R$ '.cal($veiculoValor, $valorHora+($valorHora/2));
    echo 'Paga R$ '.($uma_hora + $meio_hora_adi);
} elseif ($permanencia > 90 && $permanencia <= 120) {
    echo 'Pagar 2:00 horas'.'<br>';
    echo 'Paga R$ '.($uma_hora + $uma_hora_adi);
} elseif ($permanencia > 120 && $permanencia <= 150) {
    echo 'Pagar 2:30 horas '.'<br>';
    echo 'Paga R$ '.($uma_hora+$uma_hora_adi + $meio_hora_adi);
} elseif ($permanencia > 150 && $permanencia <= 180) {
    echo 'Pagar 3:00 horas '.'<br>';
}
        


?>