<?php
    $num1 = $num2 = $operacion = $resultado = null;

    if(isset($_POST['num1']) && isset($_POST['num2']) && isset($_POST['operacion'])){
        $num1 = $_POST['num1'];
        $num2 = $_POST['num2'];
        $operacion = $_POST['operacion'];

        if(!is_numeric($num1) || !is_numeric($num2)){
            $resultado = "Por favor, insira apenas números válidos.";
        } else {
            switch($operacion){
                case '+':
                    $resultado = $num1 + $num2;
                    break;
                case '-':
                    $resultado = $num1 - $num2;
                    break;
                case '*':
                    $resultado = $num1 * $num2;
                    break;
                case '/':
                    if($num2 == 0){
                        $resultado = "Erro: Divisão por zero!";
                    } else {
                        $resultado = $num1 / $num2;
                    }
                    break;
                default:
                    $resultado = "Operação inválida.";
            }
        }
        echo "O resultado da operação é: $resultado";
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>calculadora</title>
</head>
<body>
    <form method="post">
        <input type="number" name="num1" placeholder="Digite o primeiro número" required>
        <input type="number" name="num2" placeholder="Digite o segundo número" required>
        <input type="submit" name="operacion" value="+">
        <input type="submit" name="operacion" value="-">
        <input type="submit" name="operacion" value="*">
        <input type="submit" name="operacion" value="/">       
    </form>
</body>
</html>
