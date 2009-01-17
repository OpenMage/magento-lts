<?php
//****************************************************************************
// phpDBTree 1.4
//****************************************************************************
//      Author: Maxim Poltarak  <maxx at e dash taller dot net>
//         WWW: http://dev.e-taller.net/dbtree/
//    Category: Databases
// Description: PHP class implementing a Nested Sets approach to storing
//              tree-like structures in database tables. This technique was
//              introduced by Joe Celko <http://www.celko.com/> and has some
//              advantages over a widely used method called Adjacency Matrix.
//****************************************************************************
// The lib is FREEWARE. That means you may use it anywhere you want, you may
// do anything with it. The Author mentioned above is NOT responsible for any
// consequences of using this library.
// If you don't agree with this, you are NOT ALLOWED to use the lib!
//****************************************************************************
// You're welcome to send me all improvings, feature requests, bug reports...
//****************************************************************************
// SAMPLE DB TABLE STRUCTURE:
//
// CREATE TABLE categories (
//   cat_id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
//   cat_left    INT UNSIGNED NOT NULL,
//   cat_right    INT UNSIGNED NOT NULL,
//   cat_level    INT UNSIGNED NOT NULL,
//   PRIMARY KEY(cat_id),
//   KEY(cat_left, cat_right, cat_level)
// );
//
// This is believed to be the optimal Nested Sets use case. Use `one-to-one`
// relations on `cat_id` field between this `structure` table and
// another `data` table in your database.
//
// Don't forget to make a single call to clear()
// to set up the Root node in an empty table.
//
//****************************************************************************
// NOTE: Although you may use this library to retrieve data from the table,
//         it is recommended to write your own queries for doing that.
//         The main purpose of the library is to provide a simpler way to
//         create, update and delete records. Not to SELECT them.
//****************************************************************************
//
// IMPORTANT! DO NOT create either UNIQUE or PRIMARY keys on the set of
//            fields (`cat_left`, `cat_right` and `cat_level`)!
//            Unique keys will destroy the Nested Sets structure!
//
//****************************************************************************
// CHANGELOG:
// 16-Apr-2003 -=- 1.1
//            - Added moveAll() method
//            - Added fourth parameter to the constructor
//            - Renamed getElementInfo() to getNodeInfo() /keeping BC/
//            - Added "Sample Table Structure" comment
//            - Now it trigger_error()'s in case of any serious error, because if not you
//              will lose the Nested Sets structure in your table
// 19-Feb-2004 -=- 1.2
//            - Fixed a bug in moveAll() method?
//              Thanks to Maxim Matyukhin for the patch.
// 13-Jul-2004 -=- 1.3
//            - Changed all die()'s for a more standard trigger_error()
//              Thanks to Dmitry Romakhin for pointing out an issue with
//              incorrect error message call.
// 09-Nov-2004 -=- 1.4
//            - Added insertNear() method.
//              Thanks to Michael Krenz who sent its implementation.
//            - Removed IGNORE keyword from UPDATE clauses in insert() methods.
//              It was dumb to have it there all the time. Sorry. Anyway, you had
//              to follow the important note mencioned above. If you hadn't, you're
//              in problem.
//
//****************************************************************************
// Note: For best viewing of the code Tab size 4 is recommended
//****************************************************************************

class CDBTree {
    var $db;    // CDatabase class to plug to
    var $table;    // Table with Nested Sets implemented
    var $id;    // Name of the ID-auto_increment-field in the table.

    // These 3 variables are names of fields which are needed to implement
    // Nested Sets. All 3 fields should exist in your table!
    // However, you may want to change their names here to avoid name collisions.
    var $left = 'cat_left';
    var $right = 'cat_right';
    var $level = 'cat_level';

    var $qryParams = '';
    var $qryFields = '';
    var $qryTables = '';
    var $qryWhere = '';
    var $qryGroupBy = '';
    var $qryHaving = '';
    var $qryOrderBy = '';
    var $qryLimit = '';
    var $sqlNeedReset = true;
    var $sql;    // Last SQL query

//************************************************************************
// Constructor
// $DB : CDatabase class instance to link to
// $tableName : table in database where to implement nested sets
// $itemId : name of the field which will uniquely identify every record
// $fieldNames : optional configuration array to set field names. Example:
//                 array(
//                    'left' => 'cat_left',
//                    'right' => 'cat_right',
//                    'level' => 'cat_level'
//                 )
    function CDBTree(&$DB, $tableName, $itemId, $fieldNames=array()) {
        if(empty($tableName)) trigger_error("phpDbTree: Unknown table", E_USER_ERROR);
        if(empty($itemId)) trigger_error("phpDbTree: Unknown ID column", E_USER_ERROR);
        $this->db = $DB;
        $this->table = $tableName;
        $this->id = $itemId;
        if(is_array($fieldNames) && sizeof($fieldNames))
            foreach($fieldNames as $k => $v)
                $this->$k = $v;
    }

//************************************************************************
// Returns a Left and Right IDs and Level of an element or false on error
// $ID : an ID of the element
    function getElementInfo($ID) { return $this->getNodeInfo($ID); }
    function getNodeInfo($ID) {
        $this->sql = 'SELECT '.$this->left.','.$this->right.','.$this->level.' FROM '.$this->table.' WHERE '.$this->id.'=\''.$ID.'\'';
        if(($query=$this->db->query($this->sql)) && ($this->db->num_rows($query) == 1) && ($Data = $this->db->fetch_array($query)))
            return array((int)$Data[$this->left], (int)$Data[$this->right], (int)$Data[$this->level]);
        else trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);
    }

//************************************************************************
// Clears table and creates 'root' node
// $data : optional argument with data for the root node
    function clear($data=array()) {
        // clearing table
        if((!$this->db->query('TRUNCATE '.$this->table)) && (!$this->db->query('DELETE FROM '.$this->table))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        // preparing data to be inserted
        if(sizeof($data)) {
            $fld_names = implode(',', array_keys($data)).',';
            if(sizeof($data)) $fld_values = '\''.implode('\',\'', array_values($data)).'\',';
        }
        $fld_names .= $this->left.','.$this->right.','.$this->level;
        $fld_values .= '1,2,0';

        // inserting new record
        $this->sql = 'INSERT INTO '.$this->table.'('.$fld_names.') VALUES('.$fld_values.')';
        if(!($this->db->query($this->sql))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        return $this->db->insert_id();
    }

//************************************************************************
// Updates a record
// $ID : element ID
// $data : array with data to update: array(<field_name> => <fields_value>)
    function update($ID, $data) {
        $sql_set = '';
        foreach($data as $k=>$v) $sql_set .= ','.$k.'=\''.addslashes($v).'\'';
        return $this->db->query('UPDATE '.$this->table.' SET '.substr($sql_set,1).' WHERE '.$this->id.'=\''.$ID.'\'');
    }

//************************************************************************
// Inserts a record into the table with nested sets
// $ID : an ID of the parent element
// $data : array with data to be inserted: array(<field_name> => <field_value>)
// Returns : true on success, or false on error
    function insert($ID, $data) {
        if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        // preparing data to be inserted
        if(sizeof($data)) {
            $fld_names = implode(',', array_keys($data)).',';
            $fld_values = '\''.implode('\',\'', array_values($data)).'\',';
        }
        $fld_names .= $this->left.','.$this->right.','.$this->level;
        $fld_values .= ($rightId).','.($rightId+1).','.($level+1);

        // creating a place for the record being inserted
        if($ID) {
            $this->sql = 'UPDATE '.$this->table.' SET '
                . $this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'+2,'.$this->left.'),'
                . $this->right.'=IF('.$this->right.'>='.$rightId.','.$this->right.'+2,'.$this->right.')'
                . 'WHERE '.$this->right.'>='.$rightId;
            if(!($this->db->query($this->sql))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);
        }

        // inserting new record
        $this->sql = 'INSERT INTO '.$this->table.'('.$fld_names.') VALUES('.$fld_values.')';
        if(!($this->db->query($this->sql))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        return $this->db->insert_id();
    }

//************************************************************************
// Inserts a record into the table with nested sets
// $ID : ID of the element after which (i.e. at the same level) the new element
//         is to be inserted
// $data : array with data to be inserted: array(<field_name> => <field_value>)
// Returns : true on success, or false on error
    function insertNear($ID, $data) {
        if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID)))
            trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        // preparing data to be inserted
        if(sizeof($data)) {
            $fld_names = implode(',', array_keys($data)).',';
            $fld_values = '\''.implode('\',\'', array_values($data)).'\',';
        }
        $fld_names .= $this->left.','.$this->right.','.$this->level;
        $fld_values .= ($rightId+1).','.($rightId+2).','.($level);

        // creating a place for the record being inserted
        if($ID) {
            $this->sql = 'UPDATE '.$this->table.' SET '
            .$this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'+2,'.$this->left.'),'
            .$this->right.'=IF('.$this->right.'>'.$rightId.','.$this->right.'+2,'.$this->right.')'
                               . 'WHERE '.$this->right.'>'.$rightId;
            if(!($this->db->query($this->sql))) trigger_error("phpDbTree error:".$this->db->error(), E_USER_ERROR);
        }

        // inserting new record
        $this->sql = 'INSERT INTO '.$this->table.'('.$fld_names.') VALUES('.$fld_values.')';
        if(!($this->db->query($this->sql))) trigger_error("phpDbTree error:".$this->db->error(), E_USER_ERROR);

        return $this->db->insert_id();
    }


//************************************************************************
// Assigns a node with all its children to another parent
// $ID : node ID
// $newParentID : ID of new parent node
// Returns : false on error
   function moveAll($ID, $newParentId) {
      if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);
      if(!(list($leftIdP, $rightIdP, $levelP) = $this->getNodeInfo($newParentId))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);
      if($ID == $newParentId || $leftId == $leftIdP || ($leftIdP >= $leftId && $leftIdP <= $rightId)) return false;

      // whether it is being moved upwards along the path
      if ($leftIdP < $leftId && $rightIdP > $rightId && $levelP < $level - 1 ) {
         $this->sql = 'UPDATE '.$this->table.' SET '
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), '
            . $this->right.'=IF('.$this->right.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->right.'-'.($rightId-$leftId+1).', '
                           .'IF('.$this->left.' BETWEEN '.($leftId).' AND '.($rightId).', '.$this->right.'+'.((($rightIdP-$rightId-$level+$levelP)/2)*2 + $level - $levelP - 1).', '.$this->right.')),  '
            . $this->left.'=IF('.$this->left.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->left.'-'.($rightId-$leftId+1).', '
                           .'IF('.$this->left.' BETWEEN '.$leftId.' AND '.($rightId).', '.$this->left.'+'.((($rightIdP-$rightId-$level+$levelP)/2)*2 + $level - $levelP - 1).', '.$this->left. ')) '
            . 'WHERE '.$this->left.' BETWEEN '.($leftIdP+1).' AND '.($rightIdP-1)
         ;
      } elseif($leftIdP < $leftId) {
         $this->sql = 'UPDATE '.$this->table.' SET '
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), '
            . $this->left.'=IF('.$this->left.' BETWEEN '.$rightIdP.' AND '.($leftId-1).', '.$this->left.'+'.($rightId-$leftId+1).', '
               . 'IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->left.'-'.($leftId-$rightIdP).', '.$this->left.') '
            . '), '
            . $this->right.'=IF('.$this->right.' BETWEEN '.$rightIdP.' AND '.$leftId.', '.$this->right.'+'.($rightId-$leftId+1).', '
               . 'IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->right.'-'.($leftId-$rightIdP).', '.$this->right.') '
            . ') '
            . 'WHERE '.$this->left.' BETWEEN '.$leftIdP.' AND '.$rightId
            // !!! added this line (Maxim Matyukhin)
            .' OR '.$this->right.' BETWEEN '.$leftIdP.' AND '.$rightId
         ;
      } else {
         $this->sql = 'UPDATE '.$this->table.' SET '
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), '
            . $this->left.'=IF('.$this->left.' BETWEEN '.$rightId.' AND '.$rightIdP.', '.$this->left.'-'.($rightId-$leftId+1).', '
               . 'IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->left.'+'.($rightIdP-1-$rightId).', '.$this->left.')'
            . '), '
            . $this->right.'=IF('.$this->right.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->right.'-'.($rightId-$leftId+1).', '
               . 'IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->right.'+'.($rightIdP-1-$rightId).', '.$this->right.') '
            . ') '
            . 'WHERE '.$this->left.' BETWEEN '.$leftId.' AND '.$rightIdP
            // !!! added this line (Maxim Matyukhin)
            . ' OR '.$this->right.' BETWEEN '.$leftId.' AND '.$rightIdP
         ;
      }
      return $this->db->query($this->sql) or trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);
   }

//************************************************************************
// Deletes a record wihtout deleting its children
// $ID : an ID of the element to be deleted
// Returns : true on success, or false on error
    function delete($ID) {
        if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        // Deleting record
        $this->sql = 'DELETE FROM '.$this->table.' WHERE '.$this->id.'=\''.$ID.'\'';
        if(!$this->db->query($this->sql)) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        // Clearing blank spaces in a tree
        $this->sql = 'UPDATE '.$this->table.' SET '
            . $this->left.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->left.'-1,'.$this->left.'),'
            . $this->right.'=IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->right.'-1,'.$this->right.'),'
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->level.'-1,'.$this->level.'),'
            . $this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'-2,'.$this->left.'),'
            . $this->right.'=IF('.$this->right.'>'.$rightId.','.$this->right.'-2,'.$this->right.') '
            . 'WHERE '.$this->right.'>'.$leftId
        ;
        if(!$this->db->query($this->sql)) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        return true;
    }

//************************************************************************
// Deletes a record with all its children
// $ID : an ID of the element to be deleted
// Returns : true on success, or false on error
    function deleteAll($ID) {
        if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        // Deleteing record(s)
        $this->sql = 'DELETE FROM '.$this->table.' WHERE '.$this->left.' BETWEEN '.$leftId.' AND '.$rightId;
        if(!$this->db->query($this->sql)) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        // Clearing blank spaces in a tree
        $deltaId = ($rightId - $leftId)+1;
        $this->sql = 'UPDATE '.$this->table.' SET '
            . $this->left.'=IF('.$this->left.'>'.$leftId.','.$this->left.'-'.$deltaId.','.$this->left.'),'
            . $this->right.'=IF('.$this->right.'>'.$leftId.','.$this->right.'-'.$deltaId.','.$this->right.') '
            . 'WHERE '.$this->right.'>'.$rightId
        ;
        if(!$this->db->query($this->sql)) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        return true;
    }

//************************************************************************
// Enumerates children of an element
// $ID : an ID of an element which children to be enumerated
// $start_level : relative level from which start to enumerate children
// $end_level : the last relative level at which enumerate children
//   1. If $end_level isn't given, only children of
//      $start_level levels are enumerated
//   2. Level values should always be greater than zero.
//      Level 1 means direct children of the element
// Returns : a result id for using with other DB functions
    function enumChildrenAll($ID) { return $this->enumChildren($ID, 1, 0); }
    function enumChildren($ID, $start_level=1, $end_level=1) {
        if($start_level < 0) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        // We could use sprintf() here, but it'd be too slow
        $whereSql1 = ' AND '.$this->table.'.'.$this->level;
        $whereSql2 = '_'.$this->table.'.'.$this->level.'+';

        if(!$end_level) $whereSql = $whereSql1.'>='.$whereSql2.(int)$start_level;
        else {
            $whereSql = ($end_level <= $start_level)
                ? $whereSql1.'='.$whereSql2.(int)$start_level
                : ' AND '.$this->table.'.'.$this->level.' BETWEEN _'.$this->table.'.'.$this->level.'+'.(int)$start_level
                    .' AND _'.$this->table.'.'.$this->level.'+'.(int)$end_level;
        }

        $this->sql = $this->sqlComposeSelect(array(
            '', // Params
            '', // Fields
            $this->table.' _'.$this->table.', '.$this->table, // Tables
            '_'.$this->table.'.'.$this->id.'=\''.$ID.'\''
                .' AND '.$this->table.'.'.$this->left.' BETWEEN _'.$this->table.'.'.$this->left.' AND _'.$this->table.'.'.$this->right
                .$whereSql
        ));

        return $this->db->query($this->sql);
    }

//************************************************************************
// Enumerates the PATH from an element to its top level parent
// $ID : an ID of an element
// $showRoot : whether to show root node in a path
// Returns : a result id for using with other DB functions
    function enumPath($ID, $showRoot=false) {
        $this->sql = $this->sqlComposeSelect(array(
            '', // Params
            '', // Fields
            $this->table.' _'.$this->table.', '.$this->table, // Tables
            '_'.$this->table.'.'.$this->id.'=\''.$ID.'\''
                .' AND _'.$this->table.'.'.$this->left.' BETWEEN '.$this->table.'.'.$this->left.' AND '.$this->table.'.'.$this->right
                .(($showRoot) ? '' : ' AND '.$this->table.'.'.$this->level.'>0'), // Where
            '', // GroupBy
            '', // Having
            $this->table.'.'.$this->left // OrderBy
        ));

        return $this->db->query($this->sql);
    }

//************************************************************************
// Returns query result to fetch data of the element's parent
// $ID : an ID of an element which parent to be retrieved
// $level : Relative level of parent
// Returns : a result id for using with other DB functions
    function getParent($ID, $level=1) {
        if($level < 1) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

        $this->sql = $this->sqlComposeSelect(array(
            '', // Params
            '', // Fields
            $this->table.' _'.$this->table.', '.$this->table, // Tables
            '_'.$this->table.'.'.$this->id.'=\''.$ID.'\''
                .' AND _'.$this->table.'.'.$this->left.' BETWEEN '.$this->table.'.'.$this->left.' AND '.$this->table.'.'.$this->right
                .' AND '.$this->table.'.'.$this->level.'=_'.$this->table.'.'.$this->level.'-'.(int)$level // Where
        ));

        return $this->db->query($this->sql);
    }

//************************************************************************
    function sqlReset() {
        $this->qryParams = ''; $this->qryFields = ''; $this->qryTables = '';
        $this->qryWhere = ''; $this->qryGroupBy = ''; $this->qryHaving = '';
        $this->qryOrderBy = ''; $this->qryLimit = '';
        return true;
    }

//************************************************************************
    function sqlSetReset($resetMode) { $this->sqlNeedReset = ($resetMode) ? true : false; }

//************************************************************************
    function sqlParams($param='') { return (empty($param)) ? $this->qryParams : $this->qryParams = $param; }
    function sqlFields($param='') { return (empty($param)) ? $this->qryFields : $this->qryFields = $param; }
    function sqlSelect($param='') { return $this->sqlFields($param); }
    function sqlTables($param='') { return (empty($param)) ? $this->qryTables : $this->qryTables = $param; }
    function sqlFrom($param='') { return $this->sqlTables($param); }
    function sqlWhere($param='') { return (empty($param)) ? $this->qryWhere : $this->qryWhere = $param; }
    function sqlGroupBy($param='') { return (empty($param)) ? $this->qryGroupBy : $this->qryGroupBy = $param; }
    function sqlHaving($param='') { return (empty($param)) ? $this->qryHaving : $this->qryHaving = $param; }
    function sqlOrderBy($param='') { return (empty($param)) ? $this->qryOrderBy : $this->qryOrderBy = $param; }
    function sqlLimit($param='') { return (empty($param)) ? $this->qryLimit : $this->qryLimit = $param; }

//************************************************************************
    function sqlComposeSelect($arSql) {
        $joinTypes = array('join'=>1, 'cross'=>1, 'inner'=>1, 'straight'=>1, 'left'=>1, 'natural'=>1, 'right'=>1);

        $this->sql = 'SELECT '.$arSql[0].' ';
        if(!empty($this->qryParams)) $this->sql .= $this->sqlParams.' ';

        if(empty($arSql[1]) && empty($this->qryFields)) $this->sql .= $this->table.'.'.$this->id;
        else {
            if(!empty($arSql[1])) $this->sql .= $arSql[1];
            if(!empty($this->qryFields)) $this->sql .= ((empty($arSql[1])) ? '' : ',') . $this->qryFields;
        }
        $this->sql .= ' FROM ';
        $isJoin = ($tblAr=explode(' ',trim($this->qryTables))) && ($joinTypes[strtolower($tblAr[0])]);
        if(empty($arSql[2]) && empty($this->qryTables)) $this->sql .= $this->table;
        else {
            if(!empty($arSql[2])) $this->sql .= $arSql[2];
            if(!empty($this->qryTables)) {
                if(!empty($arSql[2])) $this->sql .= (($isJoin)?' ':',');
                elseif($isJoin) $this->sql .= $this->table.' ';
                $this->sql .= $this->qryTables;
            }
        }
        if((!empty($arSql[3])) || (!empty($this->qryWhere))) {
            $this->sql .= ' WHERE ' . $arSql[3] . ' ';
            if(!empty($this->qryWhere)) $this->sql .= (empty($arSql[3])) ? $this->qryWhere : 'AND('.$this->qryWhere.')';
        }
        if((!empty($arSql[4])) || (!empty($this->qryGroupBy))) {
            $this->sql .= ' GROUP BY ' . $arSql[4] . ' ';
            if(!empty($this->qryGroupBy)) $this->sql .= (empty($arSql[4])) ? $this->qryGroupBy : ','.$this->qryGroupBy;
        }
        if((!empty($arSql[5])) || (!empty($this->qryHaving))) {
            $this->sql .= ' HAVING ' . $arSql[5] . ' ';
            if(!empty($this->qryHaving)) $this->sql .= (empty($arSql[5])) ? $this->qryHaving : 'AND('.$this->qryHaving.')';
        }
        if((!empty($arSql[6])) || (!empty($this->qryOrderBy))) {
            $this->sql .= ' ORDER BY ' . $arSql[6] . ' ';
            if(!empty($this->qryOrderBy)) $this->sql .= (empty($arSql[6])) ? $this->qryOrderBy : ','.$this->qryOrderBy;
        }
        if(!empty($arSql[7])) $this->sql .= ' LIMIT '.$arSql[7];
        elseif(!empty($this->qryLimit)) $this->sql .= ' LIMIT '.$this->qryLimit;

        if($this->sqlNeedReset) $this->sqlReset();

        return $this->sql;
    }
//************************************************************************
}
?>