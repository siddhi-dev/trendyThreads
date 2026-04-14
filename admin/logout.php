<?php
require_once '../config/config.php';
require_once '../classes/Session.php';

Session::remove('admin_id');
Session::remove('admin_username');
Session::destroy();
Session::redirect('admin/login.php');
