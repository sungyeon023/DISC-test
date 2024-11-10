<?php
/*
BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
================================================================================
filename  : index.php
purpose   : 
create    : 2017-05-05
last edit : 2020-07-30
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
$page=isset($_SESSION['page'])?$_SESSION['page']:0;
$num_perpage=5;
$_SESSION['author'] = 'cahyadsn';
$_SESSION['ver']    = sha1(rand());
$version    = '0.7';                  //<-- version number
header('Expires: '.date('r'));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
//-- query data from database
$sql='SELECT * FROM disc_personalities ORDER BY no ASC';
$result=$db->query($sql);
$data=$x=array();
$no=0;
while($row=$result->fetch_object()){
   if($no!=$row->no){
      $no=$row->no;
      $x[$no]=array();
   }
   $x[$no][]=$row;
}
// Disable shuffle for debugging
if ( empty( $_GET['dbg'] ) ) {
   shuffle($x);     //<-- shuffle question data
}
$data=array();
foreach($x as $dt){
  if ( empty( $_GET['dbg'] ) ) {
    shuffle($dt); //<-- shuffle the term for each question
  }
  foreach($dt as $d){
   $data[]=$d;
  }
}
$terms=json_encode($data);
//-- layout configuration
$show_mark   = 0;                       //<-- show:1 or hide:0 the marker
$cols      = 4;                       //<-- number of columns (1..4)
$rows        = count($data)/(4*$cols); //<-- number of rows
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
  <link rel="stylesheet" href="<?php echo _ASSET;?>css/w3/w3.css">
  <link rel="stylesheet" href="<?php echo _ASSET;?>css/w3/w3-theme-<?php echo $c;?>.css" media="all" id="disc_css">
  <script src="<?php echo _ASSET;?>js/jquery.min.js"></script>
  <?php endif;?>
  <style>body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
  .w3-closebtn {text-decoration: none;float: right;font-size: 24px;font-weight: bold;color: inherit;} .w16left{padding-left:16px !important;}</style>
</head>
<body>
<div class="w3-top">
  <div class="w3-bar w3-theme-d5">
    <span class="w3-bar-item"># DiSC v<?php echo $version;?></span>
    <a href="#" class="w3-bar-item w3-button">Home</a>
      <div class="w3-dropdown-hover">
        <button class="w3-button">Themes</button>
        <div class="w3-dropdown-content w3-white w3-card-4" id="theme">
            <?php
            $color=array("black","brown","pink","orange","amber","lime","green","teal","purple","indigo","blue","cyan");
            foreach($color as $c){
               echo "<a href='#' class='w3-bar-item w3-button w3-{$c} color' data-value='{$c}'> </a>";
            }
            ?>   
         </div>
      </div>
   </div>
</div>  
<div class="w3-container">
  <form method='post' action='result.php' name="disc">
    <div class="w3-card-4">
      <div class='w3-container'>
        <h2>&nbsp;</h2>
        <h2>DiSC Personality Test</h1>
        <div class="w3-row">
          <div class="w3-col s12">
          <p>
            The purpose of the DiSC Personality Test is to help you understand yourself and others. The Profile provides a framework for looking at human behaviour while increasing your knowledge of your unique behavioural pattern. The goal of this practical approach is to help you create an environment that will ensure your success. At the same time, you will gain an appreciation for the different motivational environments required by other behavioural styles. The three interpretation stages, which progress from general to specific, will help you master the DiSC Dimensions of Behaviour approach for understanding people.
           </p>
         <div class="w3-container w3-theme-d2 w3-section">
            <span onclick="this.parentElement.style.display='none'" class="w3-closebtn">x</span>
            <h3>Instruction</h3>
            <p>Choose one <b>MOST</b> and one <b>LEAST</b> in each of the 28 groups of words.</p>
         </div>
          </div>
        </div>
        <?php for($i=0;$i<$rows;++$i):?>
        <div class="w3-row">
          <?php for($j=0;$j<$cols;++$j):?>
          <div class="w3-col m6 l3">
         <?php if($i*$cols+$j<28) :?>
         <table class='w3-table'>
              <thead>
              <tr class='w3-theme-d3'>
                <th>No</th>
                <th>Term</th>
                <th>Most</th>
                <th>Least</th>
              </tr>
              </thead>
              <tbody>
              <?php
              if(isset($_GET['auto'])){
                $m=rand(0,3);
                $l=rand(0,3);
                $l=$l==$m?(($m+1)%4):$l;
              } else{
                $m=$l='a';
              }
              for($n=0;$n<4;++$n): 
            $idx=($i*$cols+$j)*4+$n;
                $num=$i*$cols+$j+1;
                echo '<tr'.($num%2==0?' class="w3-theme-l3"':'').'>';
              ?>
                  <?php if($n==0):?>
                  <th rowspan='4'><?php echo $num;?></th>
                  <?php endif;?>
                  <td class='w16left'><?php echo $data[$idx]->term; ?></td>
                  <td><input type='radio' class='w3-radio' id='m_<?php echo "{$num}_{$n}";?>' name='m[<?php echo $num;?>]' value='<?php echo $data[$idx]->most;?>' required<?php echo ($m===$n?' checked':'');?>></td>
                  <td><input type='radio' class='w3-radio' id='l_<?php echo "{$num}_{$n}";?>' name='l[<?php echo $num;?>]' value='<?php echo $data[$idx]->least;?>' required<?php echo ($l===$n?' checked':'');?>></td>
                </tr>
              <?php endfor;?>
              </tbody>
            </table>
         <?php endif;?>
          </div>
          <?php endfor;?>
        </div>
        <?php endfor;?>
        <div class="w3-row">
          <div class="w3-col s12">
            <h6>&nbsp;</h6>
            <input type='submit' value='process' class='w3-button w3-round-large w3-theme-d1 w3-right w3-margin-8'/> 
          </div>
        </div>
        <h2>&nbsp;</h2>
      </div>
    </div>
  </form>
</div>
<div class="w3-bottom">
   <div class="w3-bar w3-theme-d4 w3-center w3-padding">
      DiSC Personality Test v<?php echo $version;?> copyright &copy; 2017<?php echo date('Y')>2017?'-'.date('Y'):'';?> by <a href="mailto:cahyadsn@gmail.com">cahya dsn</a><br />
   </div>
</div>
<div id="warning" class="w3-modal">
  <div class="w3-modal-content">
    <header class="w3-container w3-red"> 
      <span onclick="document.getElementById('warning').style.display='none'" class="w3-closebtn w3-hover-red w3-container w3-padding-8 w3-display-topright" title="Close Modal">&times;</span>
      <h2>Warning</h2>
    </header>
    <div class="w3-container">
      <p id='msg'></p>
    </div>
    <footer class="w3-container w3-border-top w3-padding-16 w3-light-grey">
      <a href='#' onclick="document.getElementById('warning').style.display='none'" class="w3-button w3-grey">close</a>
    </footer>
  </div>
</div>
<script src="<?php echo _ASSET;?>js/disc.v6.php?v=<?php echo md5(filemtime(_ASSET.'js/disc.v6.php'));?>"></script>     
</body>
</html>