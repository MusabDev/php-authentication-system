<?php

const BASE_URL = 'http://localhost:8000'; // Put your website url here

// Database configuration
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'authentication_system';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// SMTP connection
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_USER = 'email@domain.com';
const SMTP_PASS = 'password';
const SMTP_PORT = 465;
