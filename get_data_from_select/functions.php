<?php
function getTablesFromQuery($query) {
    preg_match_all('/\bfrom\b\s+(\w+)|\bjoin\b\s+(\w+)/i', $query, $matches);
    return array_unique(array_filter(array_merge($matches[1], $matches[2])));
}

function getPrimaryKeyFieldName($conn, $tableName) {
    $result = mysqli_query($conn, "SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['Column_name'];
    } else {
        $result = mysqli_query($conn, "SHOW COLUMNS FROM $tableName");
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['Field'];
        } else {
            return null;
        }
    }
}

function getJoinClauses($query) {
    preg_match('/from\b(.*)/i', $query, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}

function getPrimaryKeyValues($conn, $table, $primaryKey, $joinClause) {
    $query = "SELECT distinct $table.$primaryKey  From $joinClause";
    $result = mysqli_query($conn, $query);
    $primaryKeys = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $primaryKeys[] = $row[$primaryKey];
    }
    $primaryKeys = array_unique($primaryKeys);
    return $primaryKeys;
}

function getJoiningFieldName($query, $table) {
    preg_match('/' . $table . '\.(\w+)/i', $query, $matches);
    return isset($matches[1]) ? $matches[1] : null;
}

function getJoiningFieldValues($conn, $table, $joiningField, $joinClause, $whereClause='') {
    $query = "SELECT $table.$joiningField from $joinClause";
    if(!empty( $whereClause)){
        $query.= " where ". $whereClause;
    }
    $result = mysqli_query($conn, $query);
    $joiningFieldValues = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $joiningFieldValues[] = $row[$joiningField];
    }
    return $joiningFieldValues;
}

function getTableData($conn, $table, $primaryKey, $primaryKeyValues, $whereCondition='') {
    $primaryKeyValuesString = implode(',', array_map('intval', $primaryKeyValues));
    $query = "SELECT * FROM $table WHERE $primaryKey IN ($primaryKeyValuesString) ";
    if(!empty($whereCondition)){
        $query.=" and ".$whereCondition;
    }
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

function getHierarchicalData($conn, $tables, $joinClause, $joinKeys, &$dataHierarchy, $whereClause='') {
    if (count($tables) == 0) {
        return;
    }

    $table = array_shift($tables);
    if (!empty($table)) {
        $joinKey = $joinKeys[$table];
        $joinKeyValues = getPrimaryKeyValues($conn, $table, $joinKey, $joinClause);
       
        $rows = getTableData($conn, $table, $joinKey, $joinKeyValues, $whereClause);
        $dataHierarchy[$table] = $rows;
        foreach ($dataHierarchy[$table] as &$row) {
            $row['children'] = [];
            $nextWhereClause = $joinKey." IN (".$row[$joinKey].")";
            getHierarchicalData($conn, $tables, $joinClause, $joinKeys, $row['children'], $nextWhereClause);
        }
    }
}
?>
