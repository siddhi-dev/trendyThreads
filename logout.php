<?php
require_once 'config/config.php';
require_once 'classes/Session.php';

Session::remove('user_id');
Session::remove('user_name');
Session::remove('user_email');
Session::destroy();
Session::redirect('index.php');
