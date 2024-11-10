<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename  : result.php
purpose   :
create    : 2017-05-05
last edit : 2019-07-30
author    : cahya dsn
================================================================================
This program is free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free Software
Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

copyright (c) 2017-2020 by cahya dsn; cahyadsn@gmail.com
================================================================================
*/
session_start();
include 'inc/config.php';
$c=isset($_SESSION['c'])?$_SESSION['c']:(isset($_GET['c'])?$_GET['c']:'indigo');
$num_perpage=5;
$_SESSION['author'] = 'cahyadsn';
$_SESSION['ver']    = sha1(rand());
$version    = '0.7';                  //<-- version number
header('Expires: '.date('r'));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
if(isset($_POST['m']) && isset($_POST['l'])){
  $most=array_count_values($_POST['m']);
  $least=array_count_values($_POST['l']);
  $result=array();
  $aspect=array('D','I','S','C','#');
  foreach($aspect as $a){
    $result[$a]['most']=isset($most[$a])?$most[$a]:0;
    $result[$a]['least']=isset($least[$a])?$least[$a]:0;
    $result[$a]['change']=($a!='#'?$result[$a]['most']-$result[$a]['least']:0);
  }
  function getDISCResult($db,$result,$line){
    $graph=array('most'=>1,'least'=>2,'change'=>3);
    $sql="
      SELECT
        d.d,i.i,s.s,c.c,d.di,i.ii,s.si,c.ci
      FROM
        (SELECT segment d,intensity di FROM disc_results WHERE graph={$graph[$line]} AND dimension='D' AND value={$result['D'][$line]}) d,
        (SELECT segment s,intensity si FROM disc_results WHERE graph={$graph[$line]} AND dimension='I' AND value={$result['S'][$line]}) s,
        (SELECT segment i,intensity ii FROM disc_results WHERE graph={$graph[$line]} AND dimension='S' AND value={$result['I'][$line]}) i,
        (SELECT segment c,intensity ci FROM disc_results WHERE graph={$graph[$line]} AND dimension='C' AND value={$result['C'][$line]}) c
       ";
    $result=$db->query($sql);
    $data=$result->fetch_object();
    $result->free();
    return $data;
  }
  function getPatterns($db,$result,$line){
    $graph=array('most'=>1,'least'=>2,'change'=>3);
    $disc=getDISCResult($db,$result,$line);
    $sql="
      SELECT
        a.*,c.*
      FROM
        disc_pattern_map a
        JOIN disc_patterns c ON c.id=a.pattern
      WHERE
        a.d={$disc->d} AND a.i={$disc->i} AND a.s={$disc->s} AND a.c={$disc->c}
      ";
    $result=$db->query($sql);
    $data=$result->fetch_object();
    $_SESSION["s{$graph[$line]}"]="{$data->d}-{$data->i}-{$data->s}-{$data->c}";
    $_SESSION["i{$graph[$line]}"]="{$disc->di}-{$disc->ii}-{$disc->si}-{$disc->ci}";
    $_SESSION["p{$graph[$line]}"]=$data->name;
    $out="
    <div class='w3-row'>
      <div class='w3-col m8 l4'>
        <img src='"._ASSET."img/graph.php?g={$graph[$line]}' id='graph{$graph[$line]}'>
      </div>
      <div class='w3-col m4 l8'>
      <b>Segment : </b><br />{$data->d}-{$data->i}-{$data->s}-{$data->c}<br />
      <input type='hidden' id='s{$graph[$line]}' value='{$data->d}-{$data->i}-{$data->s}-{$data->c}'/>
      <input type='hidden' id='i{$graph[$line]}' value='{$disc->di}-{$disc->ii}-{$disc->si}-{$disc->ci}'/>
      <b>Classical Pattern : </b><br />{$data->name}<br />
      <input type='hidden' id='p{$graph[$line]}' value='{$data->name}'/>
      <b>Emotions : </b><br />{$data->emotions}<br />
      <b>Goal : </b><br />{$data->goal}<br />
      <b>Judges others by : </b><br />{$data->judges_others}<br />
      <b>Influences others by: </b><br />{$data->influences_others}<br />
      <b>Value to the organization: </b><br />{$data->organization_value}<br />
      <b>Overuses : </b><br />{$data->overuses}<br />
      <b>Under pressure : </b><br />{$data->under_pressure}<br />
      <b>Fears : </b><br />{$data->fear}<br />
      <b>Would increase effectiveness through: </b><br />{$data->effectiveness}<br />
      <b>Description : </b><br />{$data->description}<br />
      </div>
    </div>";
    return array($disc,$out);
  }
  /**/
  $line1=getPatterns($db,$result,'most');
  $line2=getPatterns($db,$result,'least');
  $line3=getPatterns($db,$result,'change');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>DiSC Personality Test <?php echo $version;?> </title>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta http-equiv="content-language" content="en" />
  <meta name="author" content="Cahya DSN" />
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
  <meta name="keywords" content="DiSC, personality, test" />
  <meta name="description" content="DiSC Personality Test ver <?php echo $version;?> created by cahya dsn" />
  <meta name="robots" content="index, follow" />
  <link rel="shortcut icon" href="<?php echo _ASSET;?>img/favicon.ico" type="image/x-icon">
  <?php if(defined('_ISONLINE') && _ISONLINE):?>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-<?php echo $c;?>.css" media="all" id="disc_css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
  <?php else:?>
  <link rel="stylesheet" href="<?php echo _ASSET;?>css/w3.css">
  <link rel="stylesheet" href="<?php echo _ASSET;?>css/w3-theme-<?php echo $c;?>.css" media="all" id="disc_css">
  <script src="<?php echo _ASSET;?>js/jquery.min.js"></script>
  <script src="<?php echo _ASSET;?>js/jspdf.min.js"></script>
  <script src="<?php echo _ASSET;?>js/jspdf.plugin.align.js"></script>
  <script src="<?php echo _ASSET;?>js/jspdf.plugin.autotable.js"></script>
  <?php endif;?>
  <style>body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}</style>
</head>
<body>
<div class="w3-top">
  <div class="w3-bar w3-theme-d5">
    <span class="w3-bar-item"># DiSC v<?php echo $version;?></span>
    <a href="index.php" class="w3-bar-item w3-button">Home</a>
    <!--a href="#about" class="w3-bar-item w3-button">About</a//-->
    <div class="w3-dropdown-hover">
      <button class="w3-button">Themes</button>
      <div class="w3-dropdown-content w3-white w3-card-4" id="theme">
        <?php
        $color=array("black","brown","pink","orange","amber","lime","green","teal","purple","indigo","blue","cyan");
        foreach($color as $c){
          echo "<a href='#' class='w3-bar-item w3-button w3-{$c} color_graph' data-value='{$c}'> </a>";
        }
        ?>
      </div>
    </div>
    <?php /* */?>
    <div class="w3-dropdown-hover">
      <button class="w3-button">Export</button>
      <div class="w3-dropdown-content w3-bar-block w3-card-4">
      <!--a href='#' class='w3-bar-item w3-button' onclick='exportToExcel();'>Excel</a//-->
      <a href='#' class='w3-bar-item w3-button' onclick='exportToPDF();'>PDF</a>
      </div>
    </div>
    <?php /* */?>
  </div>
</div>
<div class="w3-container">
  <div class="w3-card-4">
    <div class='w3-container'>
      <h2>&nbsp;</h2>
      <h2 class='w3-text-theme'>DiSC Personality Test Result</h1>
     <div class="w3-row">
<div class="w3-col s12 w3-padding">
<?php
$summ=array('',-100);
foreach($result as $k=>$v){
if($v['change']>$summ[1] && $k!='#') $summ=array($k,$v['change']);
}
$name=array('D'=>'Dominance','I'=>'Influence','S'=>'Steadiness','C'=>'Conscientiousness');
?>
<p>Hi, your highest dimension(s) - based on your responses to your perceptions of the
environment and the amount of control you feel you have in that environment - is <b><?php echo "{$name[$summ[0]]} ({$summ[0]}) ";?></b>.
<?php /*
<p><i>Based on the pattern of your high and low plotting points on all four DiSC Dimensions of Behavior.</i></p>
<p>Behavioral patterns, determined by the shape of your profile graph, provide an integrated interpretation of your behavioral style. Each Classical Pattern describes the behavior of people with a specific blend of the four DiSC behavioral styles, or dimensions. This description reflects the complexity and subtlety of behavior.</p>
<p>
Insights into your work behavior and the work behavior of others are summarized in nine key areas under the following headings:
<ul>
<li><b>Emotions</b>: your general demeanor</li>
<li><b>Goal</b>: what you are most motivated to obtain</li>
<li><b>Judges others by</b>: how you evaluate others</li>
<li><b>Influences others by</b>: how you affect others' behavior</li>
<li><b>Value to the organization</b>: how you contribute</li>
<li><b>Overuses</b>: how your strengths can become limitations</li>
<li><b>Under pressure</b>: how you react to stressful situations</li>
<li><b>Fears</b>: what causes you discomfort </li>
<li><b>Would increase effectiveness through</b>: how to achieve maximum success</li>
</ul>
</p>
*/?>
<hr>
<h3 class='w3-text-theme'>SUMMARY OF INTERPRETATION</h3>
<p>Here is a summary that shows how your personal report was generated. Graph III is the result of
combining your "Most" choices with your "Least" choices and is used to determine your highest DiSC
dimension, your Intensity Index scores, and your Classical Profile Pattern. If you would like more
information about how your personal report was built, please talk to your facilitator.</p>
        </div>
      </div>
      <div class="w3-row">
        <div class="w3-col s12">
          <div class="w3-row">
            <a href="javascript:void(0)" onclick="openTabs(event, 'Change');">
              <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding  w3-border-theme">GRAPH III : <b>CHANGE</b></div>
            </a>
            <a href="javascript:void(0)" onclick="openTabs(event, 'Most');">
              <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">GRAPH I : <b>MOST</b></div>
            </a>
            <a href="javascript:void(0)" onclick="openTabs(event, 'Least');">
              <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">GRAPH II : <b>LEAST</b></div>
            </a>
          </div>
          <div id="Change" class="w3-container tabs">
            <h2>Change</h2>
            <?php echo $line3[1];?>
          </div>
          <div id="Most" class="w3-container tabs" style="display:none">
            <h2>Most</h2>
            <?php echo $line1[1];?>
         </div>
          <div id="Least" class="w3-container tabs" style="display:none">
            <h2>Least</h2>
            <?php echo $line2[1];?>
          </div>
      </div>
        <div class="w3-row w3-padding">
<b>Highest DiSC Dimension(s)</b>: <span class='w3-text-theme'><?php echo "{$name[$summ[0]]} ({$summ[0]})";?></span><br>
<b>Classical Pattern</b>: <span class='w3-text-theme'><?php echo $_SESSION['p3'];?></span><br>
<b>Segment Numbers</b>: <span class='w3-text-theme'><?php echo $_SESSION['s3'];?></span><br>
        </div>
        </div>
      </div>
     <h2>&nbsp;</h2>
   <footer class="w3-container w3-theme-l1">
        <div class="w3-row">
      <div class="w3-col s12 w3-padding">
      <b>source code (v0.1) </b> : <a href='https://github.com/cahyadsn/disc'>https://github.com/cahyadsn/disc</a>
      </div>
    </div>
   </footer>
   <h2>&nbsp;</h2>
    </div>
  </div>
</div>
<div class="w3-bottom">
  <div class="w3-bar w3-theme-d4 w3-center w3-padding">
    DiSC Personality Test v<?php echo $version;?> copyright &copy; 2017<?php echo date('Y')>2017?'-'.date('Y'):'';?> by <a href="mailto:cahyadsn@gmail.com">cahya dsn</a><br />
  </div>
</div>
<script src="<?php echo _ASSET;?>js/disc.v6.php?v=<?php echo md5(filemtime(_ASSET.'js/disc.v6.php'));?>"></script>
</body>
</html>