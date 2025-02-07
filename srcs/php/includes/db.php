<?php
// app/config/database.php
function getConnection() {
    return  pg_connect("host=db port=5432 dbname=camagru user=camagru_user password=secure_password123");
}

