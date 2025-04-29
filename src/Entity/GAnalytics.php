<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace utb\GAnalytics;

/**
 * Description of GAnalytics
 *
 * @author edem
 */
class GAnalytics
{
  private $email;
  private $passwd;
  private $ids;
  private $auth;
  private static $instance;

  private function __construct()
  {
    $this->login('edem.aholouvi@ace3i.com', 'master9', 'UA-48180851-1');
  }

  private function __clone()
  {}

  public static function instance()
  {
    if(!isset(self::$instance))
    {
      $c = __CLASS__;
      self::$instance = new $c;
    }

    return self::$instance;
  }

  private function login($email, $passwd, $ids)
  {
    $this->email = $email;
    $this->passwd = $passwd;
    $this->ids = $ids;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/accounts/ClientLogin");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $data = array('accountType' => 'GOOGLE',
      'Email' => $this->email,
      'Passwd' => $this->passwd,
      'source'=>'CLI_GAnalytics',
      'service'=>'analytics');

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $hasil = curl_exec($ch);
    $hasil = @split("Auth=", $hasil);
    curl_close($ch);

    $this->auth = $hasil[1];
  }

  public function getDimensionByMetric($metrics, $dimensions, $date_1, $date_2 = null)
  {
    if(!$date_2)
    $date_2 = $date_1;

    $ch = curl_init("https://www.google.com/analytics/feeds/data?ids=ga:" . $this->ids . "&metrics=ga:" . $metrics . "&dimensions=ga:" . $dimensions . "&start-date=" . $date_1 . "&end-date=" . $date_2);

    $header[] = 'Authorization: GoogleLogin auth=' . $this->auth;

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    $infos = curl_getinfo($ch);
    curl_close($ch);

    if($infos['http_code'] != 200)
      throw new Exception("[EXCEPTION] (" . $info['http_code'] . ") " . $response);

    $XML_response = @str_replace('dxp:','',$response);
    $XML_object = simplexml_load_string($XML_response);

    $data = '';
    $label = '';
    foreach($XML_object->entry as $m)
    {
      $tmp = @split('ga:' . $dimensions . '=', $m->title);

      if($label == "")
      {
        $label .= $tmp[1] . ' (' . $m->metric['value'] . ')';
        $data .= $m->metric['value'];
      }
      else
      {
        $label .= '|' . $tmp[1] . ' (' . $m->metric['value'] . ')';
        $data .= ',' . $m->metric['value'];
      }
    }

    return array('label' => $label, 'data' => $data);
  }

  public function getMetric($metric, $date_1, $date_2 = null)
  {
    if(!$date_2)
      $date_2 = $date_1;

    $ch = curl_init("https://www.google.com/analytics/feeds/data?ids=ga:" . $this->ids . "&metrics=ga:" . $metric . "&start-date=" . $date_1 . "&end-date=" . $date_2);

    $header[] = 'Authorization: GoogleLogin auth=' . $this->auth;

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    $infos = curl_getinfo($ch);
    curl_close($ch);

    if($infos['http_code'] != 200)
      throw new Exception("[EXCEPTION] (" . $info['http_code'] . ") " . $response);

    $XML_response = @str_replace('dxp:','',$response);
    $XML_object = simplexml_load_string($XML_response);

    return $XML_object->entry->metric['value'] ? $XML_object->entry->metric['value'] : 0;
  }

  public function getMetricURI($metric, $uri, $date_1, $date_2 = null)
  {
    if(!$date_2)
      $date_2 = $date_1;

    $ch = curl_init("https://www.google.com/analytics/feeds/data?ids=ga:" . $this->ids . "&metrics=ga:" . $metric . "&dimensions=ga:pagePath&filters=ga:pagePath=" . $uri . "&start-date=" . $date_1 . "&end-date=" . $date_2);

    $header[] = 'Authorization: GoogleLogin auth=' . $this->auth;

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    $infos = curl_getinfo($ch);
    curl_close($ch);

    if($infos['http_code'] != 200)
    throw new Exception("[EXCEPTION] (" . $info['http_code'] . ") " . $response);

    $XML_response = @str_replace('dxp:','',$response);
    $XML_object = simplexml_load_string($XML_response);

    return $XML_object->entry->metric['value'] ? $XML_object->entry->metric['value'] : 0;
  }
}

?>
