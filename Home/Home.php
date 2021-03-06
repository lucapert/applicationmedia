<?php
require '../database_query/db_conn_query.php';
require '../parsing_date/parsing_date.php';
require ("../vendor/autoload.php");
use Facebook\Facebook;

session_start();

db_select("uf63wl4z2daq9dbb.chr7pe7iynqr.eu-west-1.rds.amazonaws.com", "etr617dmc399m7su", "yci3nnpsmy60fs4e", "pds13s1nv3nye63z");  

//eleboro i dati che sono stati inseriti nella registrazione e li inserisco nel db
//verifico che siano stati settati almeno uno dei tre social, che non sia stato premuto il bottone accedi
//e che sia la prima volta che accedo al db per caricarlo
//TODO:inserire parte per instagram
if((isset($_SESSION['facebook_id']) || isset($_SESSION['twitter_id']))&&!isset($_SESSION['btnAccedi'])&&!isset($_SESSION['inserito'])){
    $_SESSION['inserito']=true;
    //twitter non è stato inserito come social
    if(!$_SESSION['twitter_id_ok']){
        //inserisco prima i valori relativi alla tabella tb_clienti
        $client_data = ['facebook_id'=>$_SESSION['facebook_id'], 
             'twitter_id'=>false, 
             'email'=>$_SESSION['email'],
             'password'=>$_SESSION['password']];
        insert_daticliente_db($client_data);
    //facebook non inserito come social    
    }elseif(!$_SESSION['facebook_id_ok']){
        $client_data = ['facebook_id'=>false, 
             'twitter_id'=>$_SESSION['twitter_id'], 
             'email'=>$_SESSION['email'],
             'password'=>$_SESSION['password']];
        insert_daticliente_db($client_data);
    //TODO:inserire controllo instagram
    //altrimenti sono stati settati tutti    
    }else{
        //TODO:inserire id instagram
        //inserisco prima i valori relativi alla tabella tb_clienti
        $client_data = ['facebook_id'=>$_SESSION['facebook_id'], 
             'twitter_id'=>$_SESSION['twitter_id'], 
             'email'=>$_SESSION['email'],
             'password'=>$_SESSION['password']];
        insert_daticliente_db($client_data);
    }
    //se ho settato facebook scarico i dati relativi ai post nel db
    if($_SESSION['facebook_id_ok']){
        $i = 0;
        //print_r($_SESSION['facebook_data']);
        //ricerco i valori per i post di facebook e li inserisco in tb_post
        while(isset($_SESSION['facebook_data']['data'][$i])){
            if(isset($_SESSION['facebook_data']['data'][$i]['message'])){
                $body=$_SESSION['facebook_data']['data'][$i]['message'];
            }else{
                $body=$_SESSION['facebook_data']['data'][$i]['permalink_url'];
            }
            $id_post=$_SESSION['facebook_data']['data'][$i]['id'];
            $id_social = 1;
            $timestamp = parsing_facebook($_SESSION['facebook_data']['data'][$i]['created_time']);
            $user_id = getUserID($_SESSION['email']);
            $post_data = ['body'=> addslashes($body),
                          'id_post'=>$id_post,
                          'id_social'=>$id_social,
                          'timestamp'=>$timestamp,
                          'userID'=>$user_id];
            insert_post_data($post_data);   
            $i++;
        }
    }
    //se ho settato twitter 
    if($_SESSION['twitter_id_ok']){
        $i=0;
        while(isset($_SESSION['twitter_data'][$i])){
            //se il testo è presente
            if(isset($_SESSION['twitter_data'][$i]['text'])){
                $body=$_SESSION['twitter_data'][$i]['text'];
            }
            else{
                $body="Testo non inserito nel post";
            }
            $id_post=$_SESSION['twitter_data'][$i]['id_str'];
            $id_social = 2;
            $timestamp = parsing_twitter($_SESSION['twitter_data'][$i]['created_at']);
          
            $user_id = getUserID($_SESSION['email']);
            $post_data = ['body'=> addslashes($body),
                          'id_post'=>$id_post,
                          'id_social'=>$id_social,
                          'timestamp'=>$timestamp,
                          'userID'=>$user_id];
            insert_post_data($post_data);  
            $i++;
        }
    }
    //TODO:inserire controllo per instagram
}
//prelevo lo userID a partire dalla mail    
$_SESSION['userID'] = getUserID($_SESSION['email']);
//se ho premuto il bottone accedi oppure la registrazione è andata a buon fine sono nella schermata Home
?>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width = device-width, initial-scale = 1">
            <link rel="stylesheet" href="../css/w3.css">
            <link rel="stylesheet" href="../css/mycss.css">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
            <!-- jQuery library -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <!-- Popper JS -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
            <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
            <link rel="stylesheet" href="../css/timeline.css">
            <title>Home</title>
            </head> 
        <body>
                <nav class="navbar navbar-expand-xl"style="background-color:  #10707f">
                            <table>
                                <tr>
                                    <td style="border-right: solid 1px lightgray; padding-right: 20px; padding-left: 15px">
                                        <b class="myfont w3-opacity" style="font-size: 25px; color: white">Home</b>
                                    </td>
                                    <td style="padding-left: 20px">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 15px">
                                        Menu
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
                                            <a class="dropdown-item" href="#">View all posts timeline</a>
                                            <a class="dropdown-item" href="#">View FaceBook posts time-line</a>
                                            <a class="dropdown-item" href="#">View Twitter posts time-line</a>
                                        </div>
                                    </td>
                                    <td>
                                            <form name="reload" action="Home.php" method="POST" style="margin: 0;">
                                                <button type="submit" name="reload" value="submit" class="btn-success" style="border-style: none; height: 35px; border-radius: 4px; font-size: 15px">Reload!</button>
                                            </form>
                                    </td>
                                    <td style="text-align: right" class="container">
                                        <a class="nav-item alert-link w3-hover-text-lime" style="font-size: 15px; margin-right: 50px" href="../index.php?home"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                            LogOut
                                            </a>
                                    </td>
                                </tr>
                            </table>                   
                </nav>
                    
                <div class="container" style = "margin-top: 0">
                    <div class="page-header">
                        <h1 id="timeline" class="myfont">Post-Timeline</h1>
                    </div>
                    <ul class="timeline">
                    <?php
                        //ricerco i post da stampare sulla time-line
                        $risultato = obtainPost($_SESSION['userID']);
                        $i=0;
                        while ($riga=mysqli_fetch_assoc($risultato)){
                    ?>
                    <?php
                            //gli indici pari metto i post a sinistra
                            //i dispari a destra
                            if($i%2===0){
                                echo"<li>";
                            }else{
                                echo"<li class='timeline-inverted'>";
                            }
                    ?>
                    <?php
                           //se il social è facebook il colore è arancione con il rispettivo simbolo
                           if($riga['social']==1){
                    ?>
                                <div class="timeline-badge warning"><i> <svg xmlns="http://www.w3.org/2000/svg" width="25" height="50" viewBox="3 -8 20 40" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3v2h-2c-0.269 0-0.528 0.054-0.765 0.152-0.246 0.102-0.465 0.25-0.649 0.434s-0.332 0.404-0.434 0.649c-0.098 0.237-0.152 0.496-0.152 0.765v3c0 0.552 0.448 1 1 1h2.719l-0.5 2h-2.219c-0.552 0-1 0.448-1 1v7h-2v-7c0-0.552-0.448-1-1-1h-2v-2h2c0.552 0 1-0.448 1-1v-3c0-0.544 0.108-1.060 0.303-1.529 0.202-0.489 0.5-0.929 0.869-1.299s0.81-0.667 1.299-0.869c0.469-0.195 0.985-0.303 1.529-0.303zM18 1h-3c-0.811 0-1.587 0.161-2.295 0.455-0.735 0.304-1.395 0.75-1.948 1.303s-0.998 1.212-1.302 1.947c-0.294 0.708-0.455 1.484-0.455 2.295v2h-2c-0.552 0-1 0.448-1 1v4c0 0.552 0.448 1 1 1h2v7c0 0.552 0.448 1 1 1h4c0.552 0 1-0.448 1-1v-7h2c0.466 0 0.858-0.319 0.97-0.757l1-4c0.134-0.536-0.192-1.079-0.728-1.213-0.083-0.021-0.167-0.031-0.242-0.030h-3v-2h3c0.552 0 1-0.448 1-1v-4c0-0.552-0.448-1-1-1z"></path></svg></i></div> 
                    <?php
                            }elseif($riga['social']==2){
                    ?>
                                <div class="timeline-badge info"><i><svg xmlns="http://www.w3.org/2000/svg" width="30" height="50" viewBox="-1 -2 35 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M32 7.075c-1.175 0.525-2.444 0.875-3.769 1.031 1.356-0.813 2.394-2.1 2.887-3.631-1.269 0.75-2.675 1.3-4.169 1.594-1.2-1.275-2.906-2.069-4.794-2.069-3.625 0-6.563 2.938-6.563 6.563 0 0.512 0.056 1.012 0.169 1.494-5.456-0.275-10.294-2.888-13.531-6.862-0.563 0.969-0.887 2.1-0.887 3.3 0 2.275 1.156 4.287 2.919 5.463-1.075-0.031-2.087-0.331-2.975-0.819 0 0.025 0 0.056 0 0.081 0 3.181 2.263 5.838 5.269 6.437-0.55 0.15-1.131 0.231-1.731 0.231-0.425 0-0.831-0.044-1.237-0.119 0.838 2.606 3.263 4.506 6.131 4.563-2.25 1.762-5.075 2.813-8.156 2.813-0.531 0-1.050-0.031-1.569-0.094 2.913 1.869 6.362 2.95 10.069 2.95 12.075 0 18.681-10.006 18.681-18.681 0-0.287-0.006-0.569-0.019-0.85 1.281-0.919 2.394-2.075 3.275-3.394z"></path></svg></i></div>
                    <?php
                            }
                    ?>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4 class="timeline-title">Time-Line</h4>
                                    <p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> <?php print $riga['date_time'] ?></small></p>
                                </div>
                                <!-- la proprietà word-wrap consente di spezzare le parole ed andare a capo quando occorre -->
                                <div class="timeline-body" style="word-wrap: break-word;">
                                    <?php 
                                        $stringa = explode('://', $riga['body']);
                                        if($stringa[0]==='http'||$stringa[0]==='https'){
                                            echo "<p><b>Commento assente. Link al post:</b></p><a href=".$riga['body'].">".$riga['body']."</a>";
                                        }else{ 
                                            echo"<p>".$riga['body']."</p>";
                                        } 
                                    ?>
                                </div>
                            </div>
                        </li>
                    <?php
                            $i++;
                        }       
                    ?>
                    </ul>
                </div>
            </body>
        </html>



