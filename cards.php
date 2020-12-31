<?php

// Generate Cards
function GenerateSingleCard($data) {
    $static = "";
    $static .= "<div class='card-logo'></div>";

    return '<div class="card">'. $static . $data .'</div>';
}

/**
 * @return string
 */
function GenerateCards() {
    $cards = "";
    for ($i = 1; $i <= 10; $i++) {
        $html_action = "<div class='card-data'>" . $i ."</div>";
        $html_copyright = "<div class='card-copyright'>&copy; Theodoros Ploumis 2021 - All rights reserved.</div>";

        $data = $html_action . $html_copyright;
        $cards .= GenerateSingleCard($data);
    }
    return $cards;
}


$font_size = "20px";
$bg_color = "white";
$card_bg_color = "#f5f5f5";
$card_width = "22%";
$number_width = "80px";
$border_color = "whitesmoke";

?>

<html>
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.min.css">
        <style type = "text/css">
            :root {
                --bg-color: <?php print $bg_color; ?>;
                --card-bg-color: <?php print $card_bg_color; ?>;
                --card-width: <?php print $card_width; ?>;
                --number-width: <?php print $number_width; ?>;
                --font-size: <?php print $font_size; ?>;
            }

            * {
                box-sizing: border-box;
                -moz-box-sizing: border-box;
            }

            body {
                background: var(--bg-color);
                font-size: var(--font-size);
                font-family: sans-serif;
            }

            .board {
                display: flex;
                flex-flow: wrap;
            }

            .card {
                position: relative;
                flex-grow: 1;
                flex-shrink: 1;
                padding: 20px;
                margin: 10px 0.5%;
                border-radius: 10px;
                border: 1px solid var(--bg-color);
                background-color: var(--card-bg-color);
                width: var(--card-width);
                max-width: var(--card-width);
                page-break-inside: avoid;
            }
            
            .card-logo {
                background-image: url(./assets/logo.svg);
                background-repeat: no-repeat;
                background-position: 80% 60%;
                background-size: 60%;
                position: absolute;
                left: 0;
                width: 100%;
                height: 100%;
                top: 0;
                z-index: 0;
                opacity: 0.05;
            }

            .card-data {
                font-size: 40px;
                margin: 60px auto 120px;
                height: var(--number-width);
                width: var(--number-width);
                line-height: 70px;
                text-align: center;
                font-weight: bold;
                color: #fff;
                z-index: 2;
                background: red;
                position: relative;
                border-radius: 50%;
                border: 4px solid #f4dedd;
            }

            .card-action label {
                display: none;
            }

            .card-copyright {
                border-top: 1px solid var(--bg-color);
                padding-top: 10px;
                margin-top: 10px;
                font-size: 10px;
                text-align: center;
            }

            @page {
                size: A4 landscape;
            }

            @media print {
                .board {
                    display: block;
                    margin: 0 auto;
                }

                .card {
                    float: left;
                    display: inline-block;
                }
            }

        </style>
    </head>
    <body>
        <div class="page A4 landscape">
            <div class="board sheet">
                <?php print GenerateCards(); ?>
            </div>
        </div>
    </body>
</html>