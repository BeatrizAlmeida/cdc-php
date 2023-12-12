<?php

function printUserEntries($p, $taxa, $financiado, $voltar, $meses, $entrada) {
    $html = "<p>Parcelamento: {$p} meses</p>";
    $html .= "<p>Taxa: " . number_format($taxa, 4, '.', '') . "% ao mês " .number_format( (pow(1 + ($taxa / 100), 12) - 1) * 100, 2) . "% ao ano</p>";
    $html .= "<p>Valor financiado: $" . $financiado . "</p>";
    $html .= "<p>Valor a voltar: $" . $voltar . "</p>";
    $html .= "<p>Meses a voltar: {$meses}</p>";
    $html .= "<p>Entrada: " . ($entrada == 1 ? 'true' : 'false') . "</p>";

    return $html;
}

function printCalculos($cf, $financiado, $iteracoes, $real, $corrigido, $n) {
    $cf = number_format($cf, 6, '.', '');
    $html = "<p>Coeficiente de Financiamento: {$cf}</p>";
    $html .= "<p>Prestação: {$cf} * $" . $financiado . " = $" . ($cf * $financiado) . " ao mês</p>";
    $html .= "<p>Valor Pago: $" . ($cf * $financiado * $n) . "</p>";
    $html .= "<p>Taxa real ({$iteracoes}): " . number_format($real, 4, '.', '') . "% ao mês</p>";
    $html .= "<p>Valor corrigido: $" . number_format($corrigido, 2, '.', '') . "</p>";

    return $html;
}

function printTable($n, $cf, $entrada, $financiado, $pago, $taxa, $meses) {
    $table = "<tr><th>Mês</th><th>Prestação</th><th>Juros</th><th>Amortização</th><th>Saldo Devedor</th></tr><tr><td>n</td><td>R = pmt</td><td>J = SD * t</td><td>U = pmt - J</td><td>SD = PV - U</td></tr>";
    $sd = $entrada ? $financiado - $pago : $financiado;
    $pmt = $cf * $financiado;
    $somaJuros = 0;
    $somaU = 0;

    for ($x = 0; $x <= $n; $x++) {
        if ($x == 0)
            $table .= "<tr><td>{$x}</td><td>{$pmt}</td><td>(" . number_format($taxa / 100, 4, '.', '') . ")</td><td>0.00</td><td>(" . number_format($sd, 2, '.', '') . ")</td></tr>";
        else {
            $J = ($sd * $taxa / 100);
            $somaJuros += $J;
            $U = ($pmt - $J);
            $somaU += $U;
            $sd = ($sd - $U);
            $table .= "<tr><td>{$x}</td><td>{$pmt}</td><td>{$J}</td><td>{$U}</td><td>{$sd}</td></tr>";
        }
    }

    $table .= "<tr><td>Total</td><td>" . number_format($cf * $financiado * $n, 2, '.', '') . "</td><td>{$somaJuros}</td><td>{$somaU}</td><td>" . abs($sd) . "</td></tr>";
    return $table;
}

    function calcularJurosNewtonComEntrada($vista, $prazo, $n) {
        $t_depois = $prazo / $vista;
        $t = 0;

        $iteracoes = 0;

        // Função para calcular f(x)
        function calcularFuncao($x, $b, $c) {
            global $vista, $prazo, $n;
            return $vista * $x * $b - ($prazo / $n * ($c - 1));
        }

        // Função para calcular a derivada de f(x)
        function calcularDerivada($x, $a, $b) {
            global $vista, $prazo, $n;
            return $vista * ($b + $x * ($n - 1) * $a) - $prazo * $b;
        }

        // Iteração usando o método de Newton-Raphson
        while (abs($t_depois - $t) > 1.0e-4) {
            $t = $t_depois;
            $iteracoes++;
            $a = pow($t + 1, $n - 2);
            $b = pow($t + 1, $n - 1);
            $c = pow($t + 1, $n);
            $t_depois = $t - calcularFuncao($t, $b, $c) / calcularDerivada($t, $a, $b);
        }

        // Após a convergência, calcular a taxa de juros mensal
        $taxaJurosMensal = $t_depois * 100; // Convertendo para taxa percentual

        return ["juros" => $taxaJurosMensal, "iteracoes" => $iteracoes];
    }
    
    function calcularJurosNewtonSemEntrada($vista, $prazo, $n) {
        $t_depois = $prazo / $vista;
        $t = 0;

        $iteracoes = 0;

        // Função para calcular f(x)
        function calcularFuncaoSE($x, $a) {
            global $vista, $prazo, $n;
            return $vista * $x - ($prazo / $n * (1 - $a));
        }

        // Função para calcular a derivada de f(x)
        function calcularDerivadaSE($b) {
            global $vista, $prazo;
            return $vista - $prazo * $b;
        }

        // Iteração usando o método de Newton-Raphson
        while (abs($t_depois - $t) > 1.0e-4) {
            $t = $t_depois;
            $iteracoes++;
            $a = pow($t + 1, -$n);
            $b = pow($t + 1, -$n - 1);
            $t_depois = $t - calcularFuncaoSE($t, $a) / calcularDerivadaSE($b);
        }

        // Após a convergência, calcular a taxa de juros mensal
        $taxaJurosMensal = $t_depois * 100; // Convertendo para taxa percentual

        return ["juros" => $taxaJurosMensal, "iteracoes" => $iteracoes];
    }

    function calculateCF($taxa, $prestacoes) {
        return $taxa / (1 - pow(1 + $taxa, -$prestacoes)); 
    }

    function getValorCorrigido($n, $cf, $entrada, $financiado, $pago, $taxa, $meses) {
        $table = "<tr><th>Mês</th><th>Prestação</th><th>Juros</th><th>Amortização</th><th>Saldo Devedor</th></tr><tr><td>n</td><td>R = pmt</td><td>J = SD * t</td><td>U = pmt - J</td><td>SD = PV - U</td></tr>";
        $sd = $entrada ? $financiado - $pago : $financiado;
        $pmt = $cf * $financiado;
        $somaJuros = 0;
        $somaU = 0;
        $parcelaCorrigida = $n - $meses;
        $valorCorrigido = 0;

        for ($x = 0; $x <= $n; $x++) {
            if ($x == 0)
                $table .= "<tr><td>{$x}</td><td>{$pmt}</td><td>(" . number_format($taxa / 100, 4, '.', '') . ")</td><td>0.00</td><td>{$sd}</td></tr>";
            else {
                $J = ($sd * $taxa / 100);
                $somaJuros += $J;
                $U = ($pmt - $J);
                $somaU += $U;
                $sd = ($sd - $U);
                $table .= "<tr><td>{$x}</td><td>{$pmt}</td><td>{$J}</td><td>{$U}</td><td>{$sd}</td></tr>";
            }

            if ($x == $parcelaCorrigida) {
                $valorCorrigido = $sd;
            }
        }

        $table .= "<tr><td>Total</td><td>" . number_format($cf * $financiado * $n, 2, '.', '') . "</td><td>{$somaJuros}</td><td>{$somaU}</td><td>" . abs($sd) . "</td></tr>";
        return $valorCorrigido;
    }
    
    $errorMessage = "";
if ($_POST['tax'] == 0 && $_POST['pp'] == 0) {
    $errorMessage .=
        "<p>Taxa de juros e valor final nao podem ser ambos nulos.</p>";
}
if ($_POST['tax'] == 0 && $_POST['pv'] == 0) {
    $errorMessage .=
        "<p>Taxa de juros e valor financiado nao podem ser ambos nulos.</p>";
}
if ($_POST['pv'] == 0 && $_POST['pp'] == 0) {
    $errorMessage .=
        "<p>Valor financiado e valor final nao podem ser ambos nulos.</p>";
}
if ($errorMessage != "") {
    echo json_encode(["errorMessage" => $errorMessage]);
    exit;
} else {
    $entrada = isset($_POST['dp']);
    $vista = $_POST['pv'];
    $prazo = $_POST['pp'];
    $adiantamento = $_POST['pb'];
    $taxa = 0;
    $nIteracoes = 0;
    $taxaReal = 0;
    
    if ($_POST['tax'] == 0) {
  
        if($entrada) {
 /**
         * 
         * @var object $res
         */
            $res = calcularJurosNewtonComEntrada($vista, $prazo, $_POST['np']);
        }
        else {
            $res = calcularJurosNewtonSemEntrada($vista, $prazo, $_POST['np']);
        }
        
        $taxa = $res['juros'];
        $taxaReal = $res['juros'];
        $nIteracoes = $res['iteracoes'];
    } else {
        $taxa = $_POST['tax'];
    }


    $cf = calculateCF($taxa/100, $_POST['np']);
    $corrigido = getValorCorrigido($_POST['np'], $cf, $entrada, $vista, $adiantamento, $taxa, $_POST['mon']);
    
    }
    echo json_encode([
        "parc" => $_POST['np'],
        "taxa" => $taxa,
        "ipv" =>  $_POST['pv'],
        "months" =>  $_POST['mon'],
        "entrada" => $entrada,
        "ipb" =>  $_POST['pb'],
        "cf" =>  $cf,
        "nIteracoes" =>  $nIteracoes,
        "taxaReal" =>  $taxaReal,
        "vista" => $vista,
        "adiantamento" => $adiantamento,
        "errorMessage" => $errorMessage,
        "price" => printTable($_POST['np'], $cf, $entrada, $vista, $adiantamento, $taxa, $_POST['mon']),
        "entries" => printUserEntries($_POST['np'], $taxa, $_POST['pv'], $_POST['pb'], $_POST['mon'], $entrada),
        "calculus" => printCalculos($cf, $_POST['pv'], $nIteracoes, $taxaReal, $corrigido, $_POST['np']),
        "corrigido" =>  $corrigido
    ]);

    exit;

?>