<?php

  /* RakutenBooks API
  --------------------------------------------------------- */
  const APPLICATION_ID      = '';
  const APPLICATION_SEACRET = '';
  const AFFILIATE_ID        = '';
  const ACCESS_URL          = '';

  $params = array();
  $params['format']              = 'json';
  $params['applicationId']       = APPLICATION_ID;
  $params['application_seacret'] = APPLICATION_SEACRET;
  $params['affiliateId']         = AFFILIATE_ID;
  $params['isbn']                = $isbn;

  $requestURL = ACCESS_URL;
  foreach($params as $key => $param){
    $requestURL .= "&{$key}={$param}";
  }

  /* MySQL
  --------------------------------------------------------- */
  const DSN         = 'mysql:host=localhost;dbname=bookshelf';
  const DB_USER     = 'bookshelf';
  const DB_PASSWORD = 'pass';