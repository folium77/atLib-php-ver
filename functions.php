<?php

  // NDC取得
  function isbn2ndc( $isbn ){
    if ( $isbn ) {
      $ndl = 'http://iss.ndl.go.jp/api/opensearch?isbn=';
      $xml = simplexml_load_file( $ndl . $isbn );
      $ndc = $xml->xpath( '//channel/item/dc:subject[@xsi:type="dcndl:NDC9"]' )[0] ?? $xml->xpath( '//channel/item/dc:subject[@xsi:type="dcndl:NDC"]' )[0];
      return $ndc;
    }
  }

  // 分類名取得
  function isbn2class( $isbn, $ndc ){
    if ( $isbn ) {
      $xml  = simplexml_load_file( 'ndc9.xml' );
      $ndc  = substr( $ndc, 0, 3 );
      $class = $xml->xpath( '//row[@ndc="' . $ndc . '"]' )[0];
      return $class;
    }
  }

  // 書籍情報取得
  function isbn2info( $isbn, $requestURL ){
    if ( $isbn ) {
      $request = file_get_contents( $requestURL );
      $info    = json_decode( $request, true );
      $pattern = '/(\d{2,4})[年月日]/u';
      $replace = '$1';
      $item    = $info['Items'][0]['Item'];
      $pubdate = $item['salesDate'];
      $pubdate = preg_replace( $pattern, $replace, $pubdate );
      $strlen  = mb_strlen( $pubdate );

      return array(
        'isbn'       => $item['isbn'] ?? '',
        'title'      => $item['title'] ?? '',
        'cover'      => $item['largeImageUrl'] ?? 'noimage.png',
        'author'     => $item['author'] ?? '',
        'authorKana' => $item['authorKana'] ?? '',
        'publisher'  => $item['publisherName'] ?? '',
        'pubdate'    => $pubdate ?? '',
        'price'      => $item['itemPrice'] ?? ''
      );
    }
  }

  // DB接続
  function connectDb(){
    try {
      return new PDO(DSN, DB_USER, DB_PASSWORD);
    } catch (PDOException $e) {
      echo $e->getMessage();
      exit;
    }
  }

  // DB書き込み
  function insertDb( $pdo, $detail, $ndc, $class ){
    $sql = "INSERT INTO bs_posts(isbn, ndc, class, title, author, author_kana, publisher, pubdate, addate, price, cover) VALUES (:isbn, :ndc, :class, :title, :author, :author_kana, :publisher, :pubdate, :addate, :price, :cover)";

    $stmt = $pdo->prepare($sql);
    $params = array(
      ':isbn'        => $detail['isbn'],
      ':ndc'         => $ndc,
      ':class'       => $class,
      ':title'       => $detail['title'],
      ':author'      => $detail['author'],
      ':author_kana' => $detail['authorKana'],
      ':publisher'   => $detail['publisher'],
      ':pubdate'     => $detail['pubdate'],
      ':addate'      => date('Y-m-d H:i:s'),
      ':price'       => $detail['price'],
      ':cover'       => $detail['cover']
    );
    $stmt->execute($params);
  }

  function getDb () {




  }

  // 日付フォーマット
  function dateFormat( $date ){
    $strlen  = mb_strlen( $date );
    return ( $strlen === 8 ) ? date( 'Y年m月d日',strtotime( $date ) ) : date( 'Y年m月',strtotime( $date ) );
  }