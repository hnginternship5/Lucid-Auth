<?php
session_start();
if (isset($_GET['s'])  && $_GET['s']=="start") {
  $provider = $_GET['p'];
  $domain   = $_GET['d'];

 $_SESSION['domain'] = $domain;

  if ($provider == "google") {

return redirect()->to('login/google')->send();

  }
  if ($provider == "facebook"){

  return redirect()->to('login/facebook')->send();
  }
}
if (isset($_GET['s']) && $_GET['s'] == "done") {
  $name = $_GET['n'];
  $email = $_GET['e'];
  $pic  = $_GET['p'];
  $domain = $_SESSION['domain'];
return redirect()->to(''.$domain.'auth.php?n='.$name .'&e='.  $email.'&p='.$pic.'')->send();

}
else {
die();
}
