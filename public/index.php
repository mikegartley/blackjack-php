<?php
    require_once('_classes/class.BlackJack.php');
    define( 'WP_MAX_MEMORY_LIMIT' , '512M' );

    $go = 0;

    $newGame = new BlackJack(); 

    session_start();
    if (!isset($_POST['hit']) && !isset($_POST['stand']))
    {
        $pHand[0] = $newGame->dealDeck();
        $dHand[0] = $newGame->dealDeck();
        $pHand[1] = $newGame->dealDeck();
        $dHand[1] = $newGame->dealDeck();

        $_SESSION['uHand'] = $pHand;

        $_SESSION['dHand'] = $dHand;
        $_SESSION['dVal'] = $newGame->getValue($_SESSION['dHand']);
        $_SESSION['uVal'] = $newGame->getValue($_SESSION['uHand']);

        if ($_SESSION['uVal'] == 21)
        {
            $go = $newGame->whoWon($_SESSION['uVal'], $_SESSION['dVal'], 1);
        }

    }
    else if (isset($_POST['hit']))
    {
        $_SESSION['uHand'][sizeof($_SESSION['uHand'])] = $newGame->dealDeck();
        $_SESSION['uVal'] = $newGame->getValue($_SESSION['uHand']);
        
        $_SESSION['dVal'] = $newGame->getValue($_SESSION['dVal']);
        $_SESSION['uVal'] = $newGame->getValue($_SESSION['uHand']);
        
        $go = $newGame->whoWon($_SESSION['uVal'], $_SESSION['dVal'], 0);
    }
    else if (isset($_POST['stand']))
    {
        if($_SESSION['dVal'] < 17)
        {
            $_SESSION['dHand'][sizeof($_SESSION['dHand'])] = $newGame->dealDeck(); 
        }
        $go = $newGame->whoWon($_SESSION['uVal'], $_SESSION['dVal'], 1);
    }

    if ($go == 3 || $go == 2)
    {
        if($go == 2)
        {
            $winnerMessage = "BlackJack";
        }
        else
        {
            $winnerMessage = "Winner";
        }
    }
    else if($go == 6)
    {
        $winnerMessage = "Push";
    }
    else if($go == 4)
    {
        $winnerMessage = "You Bust";
    }
    else if($go == 5 || $go == 1)
    {
        $winnerMessage = "You Lose";
    }
?>
<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/styles.css?v=5">
    </head>
    <body>


        <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->
        <?php
        if($go > 0)
        {
        echo "<div class='modal'>";

            echo "<h1>".$winnerMessage."</h1>";

        echo "</div>";
        }
        ?>
        <section class="game-board">

            <form method = "post">
                <fieldset>
                <div class="row dealer-hand">
                    <div class="dealer-cards">
                    <?php
                        for($int = 0; $int < sizeof($_SESSION['dHand']); $int++)
                            {
                                echo "<img class='cards' alt=".$_SESSION['dHand'][$int]." src=img/cards/" .$_SESSION['dHand'][$int]. ".png />"; 
                            }
                    ?>
                    </div>
                </div>
                <div class="row c-margin --top">
                    <?php
                        for($int = 0; $int < sizeof($_SESSION['uHand']); $int++)
                        {
                            echo "<img class='cards' alt=".$_SESSION['uHand'][$int]." src=img/cards/" .$_SESSION['uHand'][$int]. ".png />";
                        }
                    ?>
                </div>
                <div class="row buttons">
                    <?php
                        if($go == 0)
                        {
                            echo "<button class='o-buttons --hit' type='submit' name='hit' title='HIT'><i class='fa fa-hand-pointer-o fa-3' aria-hidden='true'></i></button>";
                            echo "<button class='o-buttons --stand' type='submit' name='stand' title='STAND'><i class='fa fa-ban' aria-hidden='true'></i></button>";
                            echo "<button class='o-buttons --new' type='submit' name='new' title='NEW GAME'><i class='fa fa-plus-circle' aria-hidden='true'></i></button>";

                        }
                        else
                        {
                            echo "<button class='o-buttons --new' type='submit' name='new' title='NEW GAME'><i class='fa fa-plus-circle' aria-hidden='true'></i></button>";

                        }
                    ?>

                </div>
                </fieldset>
            </form>
        </section>
    </body>
</html>