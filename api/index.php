<!DOCTYPE html>
<html lang="pt" xml:lang="pt" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>CDC</title>
    <meta charset="utf8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css" />
    <script src="js-webshim/minified/polyfiller.js"></script>
    <style type="text/css">
            .box {
                background-color: antiquewhite;
                box-shadow: 8px 8px 6px grey;
                width: 450px;
                border-style: solid;
                border-width: 3px;
                border-color: lightblue;
                padding-left: 10px;
                padding-right: 10px;
                padding-bottom: 10px;
                margin-left: 2px;
            }
            body {
                background-color: #f0f0f2;
                margin: 0;
                padding: 2em;
                font-family: -apple-system, system-ui, BlinkMacSystemFont,
                    "Segoe UI", "Open Sans", "Helvetica Neue", Helvetica, Arial,
                    sans-serif;
            }
            input {
                margin: 10px 3px 10px 3px;
                border: 1px solid grey;
                border-radius: 5px;
                font-size: 12px;
                padding: 5px 5px 5px 5px;
            }
            label {
                position: relative;
                top: 12px;
                width: 190px;
                float: left;
            }
            #submitButton {
                width: 80px;
                margin-left: 20px;
            }
            #errorMessage {
                color: red;
                font-size: 90% !important;
            }
            #successMessage {
                color: green;
                font-size: 90% !important;
                display: block;
                margin-top: 20px;
            }
            .button {
                font-size: 13px;
                color: red;
                background-color: #f8fad7;
            }
            .button:hover {
                background-color: #fadad7;
            }
            #draggable {
                cursor: n-resize;
            }
            #cdcfieldset {
                cursor: move;
            }
            input.currency {
                text-align: left;
                padding-right: 15px;
            }
            .input-group .form-control {
                float: none;
            }
            .input-group .input-buttons {
                position: relative;
                z-index: 3;
            }
            .messages {
                text-align: center;
            }
            h2 {
                text-align: center;
                margin: 50px;
            }
            td, th {
                border: solid 1px blueviolet;
                padding: 5px;
            }
            .price {
                border-collapse: collapse;
                border: solid 1px blueviolet;
                margin: auto;
                text-align: center;
            }
            .first-row {
                display: flex;
                justify-content: space-around;
            }
            .entries, .calculus {
                width: 30%;
                border-color: blueviolet;
                padding: 10px;
                border-radius: 10px;
                border-style: solid;
            }

        </style>
    </head>
<body>
    <fieldset id="cdcfieldset" class="draggable ui-widget-content" style="border: 1px black solid; background-color: #cac3ba; width: 400px;">
        <legend style="border: 5px lightblue solid; margin-left: 1em; background-color: #ff6347; padding: 0.2em 0.8em;">
            <strong>Crédito Direto ao Consumidor</strong>
        </legend>
        <form id="cdc_form" action="../api/action.php" method="post">
        <div class="box">
                    <span class="input-group-addon" style="color: antiquewhite"
                        >$</span
                    >
                    <label for="parc">Parcelamento:</label>
                    <input
                        id="parc"
                        type="number"
                        name="np"
                        size="5"
                        value="36"
                        min="1"
                        max="72000"
                        step="1"
                        required
                    />meses<br />

                    <span class="input-group-addon" style="color: antiquewhite"
                        >$</span
                    >
                    <label for="itax">Taxa de juros:</label>
                    <input
                        id="itax"
                        type="number"
                        name="tax"
                        size="10"
                        value="0.50"
                        min="0.0"
                        max="100.0"
                        step="any"
                        required
                    />% mês<br />

                    <span class="input-group-addon">$</span>
                    <label for="ipv">Valor Financiado: </label>
                    <input
                        id="ipv"
                        type="number"
                        name="pv"
                        value="1000"
                        min="0.0"
                        step="0.01"
                        class="form-control currency"
                        required
                    /><br />

                    <span class="input-group-addon">$</span>
                    <label for="ipp">Valor Final (opcional):</label>
                    <input
                        id="ipp"
                        type="number"
                        name="pp"
                        value="0.0"
                        min="0.0"
                        step="0.01"
                        class="form-control currency"
                        required
                    /><br />

                    <span class="input-group-addon">$</span>
                    <label for="ipb">Valor a Voltar(opcional):</label>
                    <input
                        id="ipb"
                        type="number"
                        name="pb"
                        value="121.68"
                        min="0.0"
                        step="0.01"
                        class="form-control currency"
                        required
                    /><br />

                    <span class="input-group-addon" style="color: antiquewhite"
                        >$</span
                    >
                    <label for="months">Meses a Voltar(opcional):</label>
                    <input
                        id="months"
                        type="number"
                        name="mon"
                        value="4"
                        min="0"
                        step="1"
                        required
                    /><br />

                    <label for="idp">Entrada?</label>
                    <input id="idp" type="checkbox" name="dp"/><br />
                    <label for="iprint">Imprimir?</label>
                    <input id="iprint" type="checkbox" name="print"/><br />
                </div>
            <div class="messages">
                <input id="submitButton" class="button" type="submit" value="Calcular" />
                <p>(arraste-me para reposicionar a janela)</p>
            </div>
        </form>
        <div id="errorMessage" class="messages"></div>
        <div id="successMessage" class="messages">
            <p>Se não souber a taxa de juros coloque 0%, e forneça o valor final.</p>
        </div>
    </fieldset>
    <div id="result">
        <div class="first-row">
            <div id="entries" class="entries"></div>
            <div id="calculus" class="calculus"></div>
        </div>
        <h2>Tabela Price</h2>
        <table id="price" class="price"></table>
    </div>
    <script src="LCG.js"></script>
 <script>
                $("#result").hide();

        $(document).ready(function() {
            $("#cdc_form").submit(function(e) {
               e.preventDefault(); // Impede o envio padrão do formulário

                // Envia os dados do formulário via AJAX para o PHP
                $.ajax({
                    type: "POST",
                    url: "../api/action.php",
                    data: $("#cdc_form").serialize(), // Serializa os dados do formulário
                    success: function(response) {
                        response = JSON.parse(response);
                        // Atualiza a div com o resultado retornado pelo PHP
                        if(response.errorMessage != "") {

                            $("#errorMessage").html(response.errorMessage);
                            $("#errorMessage").show();
                            $("#successMessage").hide();
                        } else {
                            $("#successMessage").show();
                            $("#result").show();
                            $("#errorMessage").hide();
                            $("#cdcfieldset").hide();

                          $("#price").html(response.price);

                            $("#entries").html(response.entries);

                            $("#calculus").html(response.calculus);

                            
                        }
                        if ($("#iprint").is(":checked")) window.print();
                    },
                error: function () {
                    alert("Erro na requisição AJAX");
                }
                });
            });
        });
    </script>
    <script>
    dragAndSave("#cdcfieldset"); // $("#cdcfieldset").draggable()

    webshims.setOptions("forms-ext", {
        replaceUI: "auto",
        types: "number",
    });
    webshims.polyfill("forms forms-ext");
    </script>
</body>
</html>
