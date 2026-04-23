<?php

namespace App\Models;

class Contracts_model extends model {

    protected $table = null;

    function __construct() {
        $this->table = 'edomain';
        parent::__construct($this->table);
    }




// function InsertInTable($name, $email)
// {
//     $col = "insert into $edomain (`".implode("` , `",array_keys($name, $email))."`)";
//     $val = " values('";    

//     foreach($fields as $key => $value) {
//         $fields[$key] = mysqli_escape_string($value);
//     }

//     $val .= implode("' , '",array_values($fields))."');";       
    
//     $fields = array();
//     return;
// }

// registerDevice();








//   function InsertInTable($id = 1) {
//         $edomain_table = $this->db->prefixTable('name');
//         $edomain_table = $this->db->prefixTable('email');
//         // $edomain_table = $this->db->prefixTable('clients');
//         // $edomain_table = $this->db->prefixTable('taxes');

//         $item_sql = "INSERT INTO edomain(name, email) 
//        $val=VALUES ('$name', '$email')";
        
//         $item = $this->db->query($item_sql)->getRow();
// }

// function InsertInTable($id = 0){
//    $sql = array(); 
// foreach( $name as $email ) {
//     $sql[] = '("'.mysql_real_escape_string($row['text']).'", '.$row['id'].')';
// }
// mysql_query('INSERT INTO edomain (name, email) VALUES '.implode(',', $sql));


// }
  
// mysql-> Create Function InsertInTable(name Varchar(50),M1 INT){
//     -> RETURNS INT
//     -> DETERMINISTIC
//     -> BEGIN
//     -> INSERT INTO edomain values('localhosthtpps', 'test@gmail.com');
//     -> RETURN 1;
//     -> END
// }


// function InsertInTable($options = array()) {
//         $edomain_table = $this->db->prefixTable('edomain');
        
//         $where = "";
//         $id = get_array_value($options, "id");
//         if ($id) {
//             $where = " AND $edomain_table.id=$id";
//         }
        
//         $is_default = get_array_value($options, "is_default");
//         if ($is_default) {
//             $where = " AND $edomain_table.is_default=1";
//         }

//         $sql = mysql_query('INSERT INTO edomain (name, email) VALUES '.implode(',', $sql));
        
       
//     }


     protected function InsertInTable(string $edomain, array $keys, array $values): string
    {
        $insertKeys    = implode(', ', $keys);
        $hasPrimaryKey = in_array('PRIMARY', array_column($this->db->getIndexData($table), 'type'), true);

      
        if ($hasPrimaryKey) {
            $sql               = 'INSERT INTO ' . $edomain . ' (' . $insertKeys . ") \n SELECT * FROM (\n";
            $selectQueryValues = [];

            foreach ($values as $value) {
                $selectValues        = implode(',', array_map(static fn ($value, $key) => $value . ' as ' . $key, explode(',', substr(substr($value, 1), 0, -1)), $keys));
                $selectQueryValues[] = 'SELECT ' . $selectValues . ' FROM DUAL';
            }

            return $sql . implode("\n UNION ALL \n", $selectQueryValues) . "\n)";
        }

        $sql = "INSERT ALL\n";

        foreach ($values as $value) {
            $sql .= '   INTO ' . $edomain . ' (' . $insertKeys . ') VALUES ' . $value . "\n";
        }

        return $sql . 'SELECT * FROM DUAL';
    }






    //  protected function _InsertInTable(string $edomain, array $keys, array $values): string
    // {
    //     return 'INSERT ' . $this->compileIgnore('edomain') . 'INTO ' . $this->getFullName($edomain) . ' (' . implode(', ', $keys) . ') VALUES ' . implode('$name', '$email');
    // }
}