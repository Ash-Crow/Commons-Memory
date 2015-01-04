<?php
include_once("lib/CommonsMemory.lib.php");
$timerStart = microtime(true);

if (isset($_REQUEST["theme"])) {
  if(!is_numeric($_REQUEST["theme"])) {
    die("Error : theme must be a numeric value");
  } else {
    $theme = $_REQUEST["theme"];
  }
} else {
  $theme = 13893548; // Default theme is animals.
}

$the_game = new CommonsMemory($theme,8);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wikimedia Commons Memory game</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link href="js/nailthumb/jquery.nailthumb.1.1.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
      .transparent {
        opacity: 0;
      }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-fixed-top navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Wikimedia Commons Memory game</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="navbar-logo"><a href="https://tools.wmflabs.org"><img title="Powered by Wikimedia Labs" src="//upload.wikimedia.org/wikipedia/commons/thumb/6/60/Wikimedia_labs_logo.svg/32px-Wikimedia_labs_logo.svg.png" /></a></li>
            <li class="navbar-logo"><a href="http://ashtree.eu/"><img title="Developed by Sylvain Boissel" src="http://ashtree.eu/avatars/logo2-32.png" /></a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </nav><!-- /.navbar -->


<br /><br /><br /><br />

    <div class="container">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-9">
          <p class="pull-right visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
          </p>
          <div class="jumbotron">
            <h1>Wikimedia Commons Memory game</h1>
            <p>Pick a theme on the right. The pictures are chosen amongst the Featured pictures of Wikimedia Commons.</p>
          </div>

          <h2 id="victory" style="display: none">You won!</h2>
          
            <?php $the_game->run(); 
            $the_game->imageList();
            ?>

        </div><!--/.col-xs-12.col-sm-9-->

        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
          <div class="list-group">
            <?php $the_game->listThemes(); ?>
          </div>
        </div><!--/.sidebar-offcanvas-->
      </div><!--/row-->

      <hr>

      <?php 
      $timerStop = microtime(true);
      $timeSpent= $timerStop - $timerStart;
      ?>

      <footer class="footer">
        <div class="container">
          <p class="text-muted">Script runtime: <?php echo round($timeSpent,2); ?> seconds.</p>
        </div>
      </footer>

    </div><!--/.container-->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" ></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/nailthumb/jquery.nailthumb.1.1.js"></script>
    <script type="text/javascript" src="js/knuth-shuffle.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $('.nailthumb-container').nailthumb({width:125,height:125,animationTime:0});
      });

      $( "#toggleImageList" ).click(function(e) {
        $( "#imagelist" ).toggle();
        e.preventDefault();
      });

      <?php $the_game->jsOutput(); ?>

      var items_number = items.length;
      var cards_number = items_number * 2;

      var cards_list = items.concat(items);
      window.knuthShuffle(cards_list);

      var shown_cards = 0;
      var card1 = "";
      var card2 = "";

      function checkCard(card_id){
        if ($("#pic"+ card_id).hasClass('hidden-card')) {
          if (shown_cards == 0) {
            shown_cards++;
            card1 = card_id;
            $("#pic"+card1).attr('src', cards_list[card1]).nailthumb({width:125,height:125,animationTime:400});
          } else {
            card2 = card_id;
            $("#pic"+card2).attr('src', cards_list[card2]).nailthumb({width:125,height:125,animationTime:400});
            window.setTimeout(compareCards,800);
            shown_cards = 0;
          }
        }
      }

      function compareCards(){
        if (cards_list[card1] == cards_list[card2]) {
          $("#pic"+card1).toggleClass("transparent hidden-card");
          $("#pic"+card2).toggleClass("transparent hidden-card");

          if ($('.hidden-card').length == 0) {
            $('#the-board').toggle();
            $('#victory').toggle();
          }
        } else {
          $("#pic"+card1).attr('src', "img/back.png");
          $("#pic"+card2).attr('src', "img/back.png");
        }
      }

      $(".hidden-card").click(function(e){
        var id = $(this).attr('id').substring(3);
        checkCard(id);        

        e.preventDefault();
      });
    </script>
  </body>
</html>