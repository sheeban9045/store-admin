<?php

namespace App\Models;

class FAQs_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'faqs';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $faqs_table = $this->db->prefixTable('faqs');
        $faq_categories_table = $this->db->prefixTable('faq_categories');

        $where = "";

        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $faqs_table.id=$id";
        }

        $category_id = get_array_value($options, "category_id");
        if ($category_id) {
            $where .= " AND $faqs_table.category_id=$category_id";
        }

        $extra_select = '';

        $limit_query = "";

        $sql = "SELECT $faqs_table.*, $faq_categories_table.title as category_title $extra_select
        FROM $faqs_table
        LEFT JOIN $faq_categories_table ON $faq_categories_table.id= $faqs_table.category_id
        WHERE $faqs_table.deleted=0 $where
        ORDER BY $faqs_table.title ASC
        $limit_query";

        return $this->db->query($sql);
    }

    function is_slug_exists($slug, $id = 0) {
        $result = $this->get_all_where(array("slug" => $slug, "deleted" => 0));
        if (count($result->getResult()) && $result->getRow()->id != $id) {
            return $result->getRow();
        } else {
            return false;
        }
    }

    // function _delete($id = 0, $undo = false)
    // {
    //    $res = $this->db->query("DELETE FROM `crm_faqs` WHERE `crm_faqs`.`id` = $id");
      
    //    return $res;
    // }

}
