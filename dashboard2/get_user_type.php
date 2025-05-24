<?php
session_start();
echo isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'student';
?> 