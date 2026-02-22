
<?php
session_start();
session_unset();//unset everything in the session.
session_destroy();

header("Location: index.php");//redirect to the landing page.
exit();
?>
