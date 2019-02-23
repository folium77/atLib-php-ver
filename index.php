<?php ini_set('display_errors', 'On'); ?>
<!DOCTYPE html>
<html>
<head>
  <title>atlib</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <header class="header">
    <div class="header-inner">
      <a href="/"><img src="logo.svg" alt="" width="150" height=""></a>
    </div>
  </header>

  <form class="text-center p-3" action="/" method="post">
    <p><label for="isbn">ISBN： </label><input id="isbn" type="text" name="isbn"> <input type="submit" name="send" value="追加"></p>
  </form>

  <div class="wrap">

    <p class="sort text-right"><a href="./">日本十進分類</a> | <a href="./?order=author_kana">著者別</a> | <a href="./?order=publisher">出版社</a> | <a href="./?order=id">登録順</a></p>

    <?php

      if( isset($_POST['isbn']) ) {
        $isbn = $_POST['isbn'];
      } else {
        $isbn = null;
      }

      require_once( 'config.php' );
      require_once( 'functions.php' );

      $detail = isbn2info( $isbn, $requestURL );
      $ndc    = isbn2ndc( $isbn );
      $class  = isbn2class( $isbn, $ndc );
      $pdo    = connectDb();
      $exists = '';
    ?>

    <?php
      $order = $_GET['order'] ?? 'ndc';
      $order = ( $order == 'id' ) ? $order . ' desc' : $order . ' ASC';
      $sql   = 'SELECT * FROM bs_posts ORDER BY ' . $order . ', pubdate ASC';
      $stmt  = $pdo->query($sql);
    ?>


    <div class="row">
      <?php
        foreach ($stmt as $row) :
        if( $isbn === $row['isbn'] ) $exists = 1;
      ?>

      <div class="col col-sm-3 col-xs-6 text-center">
        <a href="" data-toggle="modal" data-target="#exampleModal">
          <figure class="col-cover">
            <img src="<?php echo $row['cover']; ?>">
          </figure>
          <div class="text-left">
            <p class="col-title"><?php echo $row['title']; ?></p>
            <p class="col-class">NDC：<?php echo $row['ndc']; ?>／<?php echo $row['class']; ?></p>
            <p class="col-author"><?php echo $row['author']; ?><span class="col-author__kana">／<?php echo $row['author_kana']; ?></span></p>
            <p class="col-publisher">出版社：<?php echo $row['publisher']; ?></p>
            <p class="col-pubdate">発売日：<?php echo dateFormat( $row['pubdate'] ); ?></p>
            <p class="col-price">価格：&yen;<?php echo number_format( $row['price'] ); ?></p>
          </div>
        </a>
      </div>

      <?php endforeach; ?>
    </div>

    <?php if ( $isbn ) : ?>
      <div class="col-sm-3 text-center">
        <p class="text-left">
          <img src="<?php echo $detail['cover']; ?>"><br>
          書名：<?php echo $detail['title']; ?><br>
          NDC：<?php echo $ndc; ?>／<?php echo $class; ?><br>
          著者：<?php echo $detail['author']; ?>／<?php echo $detail['authorKana']; ?><br>
          出版社：<?php echo $detail['publisher']; ?><br>
          発売日：<?php echo dateFormat( $detail['pubdate'] ); ?><br>
          価格：&yen;<?php echo number_format( $row['price'] ); ?>
        </p>
      </div>

      <?php
        if ( $exists != 1 ) {
          insertDb( $pdo, $detail, $ndc, $class );
        } else {
          echo 'すでに存在します。';
        }
      ?>

    <?php endif; ?>

  </div>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">中動態の世界</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body row">

          <div class="row">
            <div class="col-sm-4">
              <figure class="col-cover">
                <img src="https://thumbnail.image.rakuten.co.jp/@0_mall/book/cabinet/1578/9784260031578.jpg?_ex=200x200">
              </figure>
            </div>
            <div class="col-sm-8">
              <table class="table">
                <tbody>
                  <tr>
                    <th scope="row">著者</th>
                    <td>國分 功一郎</td>
                  </tr>
                  <tr>
                    <th scope="row">ISBN</th>
                    <td>9784260031578</td>
                  </tr>
                  <tr>
                    <th scope="row">価格</th>
                    <td>&yen;2,160</td>
                  </tr>
                  <tr>
                    <th scope="row">発売日</th>
                    <td>2017年03月27日</td>
                  </tr>
                  <tr>
                    <th scope="row">NDC</th>
                    <td>104／論文集．評論集．講演集</td>
                  </tr>
                  <tr>
                    <th scope="row">出版社</th>
                    <td>医学書院</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="memo">
  　
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

  <button class="addbtn">＋</button>

  <footer class="footer">
    © 2018 <span>atlib</span>
  </footer>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</body>
</html>