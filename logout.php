<?php
require('vendor/autoload.php');

use aitsydney\Account;
$account = new Account();
$account -> logout();

header('location: index.php');
?>