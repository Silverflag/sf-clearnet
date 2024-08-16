<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
  echo "NO_USERNAME";
} else {
  echo "USERNAME_SET";
}
?>
