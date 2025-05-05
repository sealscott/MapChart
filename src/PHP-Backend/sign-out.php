<?php
    //DO NOT OUTPUT ANYTHING FROM THIS FILE
    //IT WILL BREAK

    //Destroying the session and redirecting to the sign-in page
    session_start();

    session_destroy();

    header('Location: /src/Pages/sign-in-page.php');
    
    exit();

