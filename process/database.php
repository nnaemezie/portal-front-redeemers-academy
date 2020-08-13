<?php

    session_start();

    class Database{
        
        public $conn;

        public function __construct(){
            $this->db_conn();
        }
        
        public function db_conn(){
            $this->conn = new mysqli(db_host, db_user, db_pass, db_name);

            if ($this->conn->connect_errno) {
                die("database connection failed ".mysqli_error());
            }
        }

        public function query($sql){
            $result = mysqli_query($this->conn, $sql);
            return $result;
        }

        private function confirm_query($result){
            if (!$result) {
                die("query failed ".mysqli_error());
            }
        }

        public function escape_string($string){
            $escape_string = mysqli_escape_string($this->conn, $string);
            return $escape_string;
        }

    }
    $database = new Database();
    