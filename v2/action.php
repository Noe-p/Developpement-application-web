<?php
//CONNEXION A LA BASE

$mysqli = new mysqli("obiwan2.univ-brest.fr", "zphilipno", "j98pkj9m", "zfl2-zphilipno");
if ($mysqli->connect_errno) {
   echo "Error: Problème de connexion à la BDD \n";
   echo "Errno: " . $mysqli->connect_errno . "\n";
   echo "Error: " . $mysqli->connect_error . "\n";

   exit();
}

if (!$mysqli->set_charset("utf8")) {
   printf("Pb de chargement du jeu de car. utf8 : %s\n", $mysqli->error);
   exit();
}

//INSCRIPTION
if ($_GET['action']=='inscription') {
   //vérification si formulaire vide
   if(!empty($_POST['pseudo']) || !empty($_POST['mdp']) || !empty($_POST['confirm_mdp']) || !empty($_POST['email']) || !empty($_POST['nom']) || !empty($_POST['prenom'])){
      $pseudo=htmlspecialchars(addslashes($_POST['pseudo']));
      $mdp=htmlspecialchars(addslashes($_POST['mdp']));
      $confirm_mdp=htmlspecialchars(addslashes($_POST['confirm_mdp']));
      $email=htmlspecialchars(addslashes($_POST['email']));
      $nom=htmlspecialchars(addslashes($_POST['nom']));
      $prenom=htmlspecialchars(addslashes($_POST['prenom']));

      //verification si le compte existe déjà
      $reqUser = "SELECT com_pseudo FROM t_compte_com WHERE com_pseudo = '$_POST[pseudo]'";
      $resUser = $mysqli->query($reqUser);


      if($resUser->num_rows == 1){
         include('inscription.php');
         echo "<script>
            document.getElementById('message2').style.color = 'rgb(210, 28, 28)';
            document.getElementById('message2').innerHTML = 'Pseudo déjà utilisé';
            document.getElementById('message2').style.fontSize = '0.8em';
            </script>";
         }
      else{
         //verification mdp
         if(strcmp($mdp,$confirm_mdp)!==0){
            include('inscription.php');
            echo "<script>
               document.getElementById('message2').style.color = 'rgb(210, 28, 28)';
               document.getElementById('message2').innerHTML = 'Les mots de passes ne sont pas identiques';
               document.getElementById('message2').style.fontSize = '0.8em';
               </script>";
         }
         //création compte
         else{
            $reqCom="INSERT INTO t_compte_com (com_pseudo, com_mdp) VALUES ('$pseudo', MD5('$mdp'))";
            $resCom= $mysqli->query($reqCom);

            if(!$resCom){
               echo "Error: La requête a échoué \n";
               echo "Query: " . $sql . "\n";
               echo "Errno: " . $mysqli->errno . "\n";
               echo "Error: " . $mysqli->error . "\n";

               $reqSuppCmp="DELETE FROM t_compte_com WHERE com_pseudo='$pseudo'";
               $resSuppCmp=$mysqli->query($reqSuppCmp);
               exit;
            }
            //création profil
            else{
               $reqPro="INSERT INTO t_profil_pro (pro_nom, pro_prenom, pro_mail, pro_validite, pro_statut, pro_date, com_pseudo) VALUES ('$nom', '$prenom', '$email', 'A', 'R', CURDATE(), '$pseudo');";
               $resPro=$mysqli->query($reqPro);

               if (!$resPro) {
                  echo "Error: La requête a échoué \n";
                  echo "Query: " . $sql . "\n";
                  echo "Errno: " . $mysqli->errno . "\n";
                  echo "Error: " . $mysqli->error . "\n";
                  exit;
               }
               //Si tout marche :
               else{
                  header("Location: connexion.php");
               }
            }
         }
      }
   }
   else{
      include('inscription.php');
      echo "<script>
         document.getElementById('message2').style.color = 'rgb(210, 28, 28)';
         document.getElementById('message2').innerHTML = 'Veuiller remplir tous les champs';
         document.getElementById('message2').style.fontSize = '0.8em';
         </script>";
   }



//CONNEXION
}elseif ($_GET['action']=='connexion') {
   $reqCom = "SELECT com_pseudo, com_mdp, pro_validite FROM t_compte_com JOIN t_profil_pro USING(com_pseudo) WHERE com_pseudo = '$_POST[pseudo]'";
   $resCom = $mysqli->query($reqCom);
   $Com = $resCom->fetch_array(MYSQLI_ASSOC);

   if (!$resCom) {
      echo "Error: La requête a échoué \n";
      echo "Query: " . $sql . "\n";
      echo "Errno: " . $mysqli->errno . "\n";
      echo "Error: " . $mysqli->error . "\n";
      exit;
   }

   if (($Com['com_pseudo'] != $_POST['pseudo']) and ($Com['com_mdp'] != md5($_POST['mdp']))) {
      include('connexion.php');

      echo "<script>
         document.getElementById('message3').style.color = 'rgb(210, 28, 28)';
         document.getElementById('message3').innerHTML = 'Mauvais pseudo ou mot de passe';
         document.getElementById('message3').style.fontSize = '0.8em';
         </script>";
   }
   else if ($Com['pro_validite']=='D') {
      include('connexion.php');

      echo "<script>
         document.getElementById('message3').style.color = 'rgb(210, 28, 28)';
         document.getElementById('message3').innerHTML = 'La connexion a échoué, compte désactivé';
         document.getElementById('message3').style.fontSize = '0.8em';
         </script>";
   }
   else {
      session_start();
      $_SESSION['pseudo'] = $_POST['pseudo'];
      include('connexion.php');

      header("Location: profil.php");

   }

//DECONNEXION
}elseif($_GET['action']=='deconnexion'){
   session_start();
   $_SESSION = array();
   session_destroy();

   header("Location: index.php");
}


$mysqli->close();
?>
