<?php
session_start();
require('php/config.php');

//information user
$reqProfil = "SELECT * FROM t_profil_pro WHERE com_pseudo = '$_SESSION[pseudo]'";
$resProfil = $con -> query($reqProfil);

$row = $resProfil->fetch_array(MYSQLI_ASSOC);

$pro_nom = $row['pro_nom'];
$pro_prenom = $row['pro_prenom'];
$pro_mail = $row['pro_mail'];
$pro_date = $row['pro_date'];
$pro_statut = $row['pro_statut'];

//information Organisation
$reqPre = "SELECT * FROM t_presentation_pre WHERE com_pseudo = '$_SESSION[pseudo]'";
$resPre = $con -> query($reqPre);
$nbRows = mysqli_num_rows($resPre);

$rowPre = $resPre->fetch_array(MYSQLI_ASSOC);

$pre_nomStruct = $rowPre['pre_nomStruct'];
$pre_adresse = $rowPre['pre_adresse'];
$pre_adresseMail = $rowPre['pre_adresseMail'];
$pre_numeroTel = $rowPre['pre_numeroTel'];
$pre_horaireOuverture = $rowPre['pre_horaireOuverture'];
$pre_texte = $rowPre['pre_texte'];

//Creation d'une organisation
if($nbRows==0 and empty($_POST['nomOrga'])==false){
   $sqlOrga="INSERT INTO `t_presentation_pre` (`pre_nomStruct`, `pre_adresse`, `pre_adresseMail`, `pre_numeroTel`, `pre_horaireOuverture`, `pre_texte`, `com_pseudo`) VALUES ('$_POST[nomOrga]', '$_POST[adresseOrga]', '$_POST[mailOrga]', '$_POST[telOrga]', '$_POST[horaireOrga]', '$_POST[descriptionOrga]', '$_SESSION[pseudo]');";

   if (mysqli_query($con, $sqlOrga)) {
      header("Refresh: 0;");
   }
   else {
      echo "Erreur Compte: " . $sqlOrga . "<br>" . mysqli_error($con);
      mysqli_close($con);
   }
}


$con->close();
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Focus-Compte</title>

      <link rel="stylesheet" href="css/profil.css" />
      <link rel="stylesheet" href="css/navBar.css" />
   </head>
   <body>


      <?php require('php/navBar.php'); ?>

      <header>
         <?php
            if($pro_statut=='R'){
               echo "<style type=\"text/css\"> .buttonAdmin {display: none;} </style>";
            }
         ?>
         <div class="buttonInfos">
            <a class="buttonProfil open" href="#">Profil</a>
            <a class="buttonAdmin" href="#">Administration</a>
         </div>

         <div class="administration">
            <form action="/my-handling-form-page" method="post">
               <h2>Connexion administrateur :</h2>
               <div>
                  <label for="adresseMail">Mail :</label>
                  <input type="email" id="adresseMail" name="email">
               </div>
               <div>
                  <label for="pass">Mot de passe :</label>
                  <input type="current-password" id="pass" name="password" minlength="8" required>
               </div>
               <div>
                  <input class="buttonConnexion" type="submit" value="Connexion">
               </div>
            </form>
         </div>

         <?php
            if($nbRows==0){
               echo "<style type=\"text/css\">.information.open .organisation {display: none;} </style>";
               echo "<style type=\"text/css\">.information.open .organisationNew {display: flex;} </style>";
            }
            else{
               echo "<style type=\"text/css\">.information.open .organisation {display: flex;} </style>";
               echo "<style type=\"text/css\">.information.open .organisationNew {display: none;} </style>";
            }
         ?>
         <div class="information open">
            <section>
               <h2>
                  <?php echo $_SESSION['pseudo'] . ' :'; ?>
               </h2>
                  <article class="infosUser">
                     <ul>
                        <li><B>Nom :</B> <?php echo $pro_nom ?> </li>
                        <li><B>Pénom :</B> <?php echo $pro_prenom ?></li>
                        <li><B>Mail :</B> <?php echo $pro_mail ?></li>
                        <li><B>Membre depuis le :</B> <?php echo $pro_date ?></li>
                     </ul>
                  </article>
            </section>

            <section class="organisationNew">
               <h2>Vous faites partie d'une organisation ? </h2>
               <form action="profil.php" method="post">
                  <article class="infosOrga">
                     <div>
                        <label for="nomOrga"><B>Nom :</B></label>
                        <input type="text" id="nomOrga" name="nomOrga" >
                     </div>
                     <div>
                        <label for="adresseOrga"><B>Adresse :</B></label>
                        <input type="text" id="adresseOrga" name="adresseOrga" >
                     </div>
                     <div>
                        <label for="mailOrga"><B>Mail :</B></label>
                        <input type="email" id="mailOrga" name="mailOrga" >
                     </div>
                     <div>
                        <label for="telOrga"><B>Téléphone :</B></label>
                        <input type="text" id="telOrga" name="telOrga" >
                     </div>
                     <div>
                        <label for="horaireOrga"><B>Horaire d'ouverture :</B></label>
                        <input type="text" id="horaireOrga" name="horaireOrga" >
                     </div>
                     <div>
                        <label for="descriptionOrga"><B>Description :</B></label>
                        <input type="text" id="descriptionOrga" name="descriptionOrga" >
                     </div>
                     <div>
                        <input class="buttonConnexionOrga" type="submit" value="Ajouter une organisation" id="submit"/>
                     </div>
                  </article>
               </form>
            </section>

            <section class="organisation">
               <h2>Où nous trouver : </h2>
                  <article class="infosUser">
                     <ul>
                        <li><B>Nom :</B> <?php echo $pre_nomStruct ?> </li>
                        <li><B>Adresse :</B> <?php echo $pre_adresse ?> </li>
                        <li><B>Mail :</B> <?php echo $pre_adresseMail ?> </li>
                        <li><B>Téléphone :</B> <?php echo $pre_numeroTel ?> </li>
                        <li><B>Horaire d'ouverture :</B> <?php echo $pre_horaireOuverture ?> </li>
                        <li><B>Decription :</B> <?php echo $pre_texte ?></li>
                     </ul>
                  </article>
            </section>
         </div>

      </header>

      <div class="buttonsSelection">
         <a href="profil.php?selection=Paysage">Paysage</a>
         <a href="profil.php?selection=Portrait">Portrait</a>
         <a href="profil.php?selection=Brest">Brest</a>
      </div>

      <h2><?php echo $_GET['selection']; ?> :</h2>

      <section class="publication">
         <article class="imgUser">
            <div class="headerPublic">
               <a href="#"> <?php echo $_SESSION['pseudo']; ?> </a>
               <h3>Rue de Brest</h3>
            </div>
            <img src="assets/img/img1.jpg" alt="img1">
            <p>Lors d'une petite ballade dans les rue de brest, du text sk,hjfklebzas:kfhbleajrqskhfcbdlqjdqkhvcb</p>
         </article>

         <article class="imgUser">
            <div class="headerPublic">
               <a href="#">Tata</a>
               <h3>Pluie sur les grues</h3>
            </div>
            <img src="assets/img/img2.jpg" alt="img2">
            <p>Quand la tempête fait rage à brest</p>
         </article>

         <article class="imgUser">
            <div class="headerPublic">
               <a href="#">Zozo</a>
               <h3>Rue de Brest</h3>
            </div>
            <img src="assets/img/img3.jpg" alt="img3">
            <p></p>
         </article>

      </section>

      <script type="text/javascript" src="js/createElement.js"></script>
      <script type="text/javascript" src="js/navBar.js"></script>
   </body>
</html>