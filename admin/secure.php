<?php

if (!isset($_SESSION['id']) || $_SESSION['role'] !== "admin") {
  header("Location: ../login.php");
  exit();
}

?>