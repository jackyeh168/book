<?php

class dbObject
{
    private $dbhost = '';
    private $dbname = '';
    private $dbuser = '';
    private $dbpass = '';

    public function __construct($dbhost, $dbname, $dbuser, $dbpass)
    {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbname = $dbname;

        $this->connect();
    }

    protected function connect()
    {
        throw new Exception('The function need to be implemented');
    }

    public function execute()
    {
        throw new Exception('The function need to be implemented');
    }
}

class mysqlDBObject extends dbObject
{
    private $link;

    public function __construct($dbhost, $dbname, $dbuser, $dbpass)
    {
        super($dbhost, $dbname, $dbuser, $dbpass);
    }

    protected function connect()
    {
        $this->link = ($GLOBALS['___mysqli_ston'] = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass));

        if (mysqli_connect_errno()) {
            throw new Exception('Conncet to Database Error: '.mysqli_connet_error(), 1);
        }

        mysqli_query($GLOBALS['___mysqli_ston'], 'SET NAMES utf8');
        mysqli_query($this->link, 'SET CHARACTER_SET_database= utf8');
        mysqli_query($this->link, 'SET CHARACTER_SET_CLIENT= utf8');
        mysqli_query($this->link, 'SET CHARACTER_SET_RESULTS= utf8');

        if (!(bool) mysqli_query($this->link, 'USE '.$this->mysql_database)
        ) {
            throw new Exception('Database '.$this->mysql_database.' does not exist!');
        }
    }

    public function execute($sql = null)
    {
        if ($sql === null) {
            return false;
        }
        $this->last_sql = str_ireplace('DROP', '', $sql);
        $result_set = array();

        $result = mysqli_query($this->link, $this->last_sql);

        if (((is_object($this->link)) ? mysqli_error($this->link) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))) {
            throw new Exception('MySQL ERROR: '.((is_object($this->link)) ? mysqli_error($this->link) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        } else {
            $this->last_num_rows = @mysqli_num_rows($result);
            for ($xx = 0; $xx < @mysqli_num_rows($result); ++$xx) {
                $result_set[$xx] = mysqli_fetch_assoc($result);
            }
            if (isset($result_set)) {
                return $result_set;
            } else {
                throw new Exception('result: zero');
            }
        }
    }

    public function __destruct()
    {
        mysqli_close($this->link);
    }
}
