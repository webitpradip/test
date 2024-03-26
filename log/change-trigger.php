<?php require_once 'menu.php';?>
<code>
    <pre>
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(255),
    column_name VARCHAR(255),
    old_value TEXT,  -- Use TEXT or appropriate data type to accommodate any value
    new_value TEXT,
    operation VARCHAR(10), -- 'INSERT', 'UPDATE', 'DELETE'
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
</pre>
</code>
<br/>
<code>
<pre>
DELIMITER $$

CREATE TRIGGER audit_trigger AFTER INSERT OR UPDATE OR DELETE ON your_table_name
FOR EACH ROW
BEGIN
    IF INSERTING THEN
        INSERT INTO audit_log (table_name, column_name, new_value, operation)
        VALUES (NEW.table_name, NEW.column_name, NEW.column_value, 'INSERT');
    ELSIF UPDATING THEN
        INSERT INTO audit_log (table_name, column_name, old_value, new_value, operation)
        VALUES (OLD.table_name, OLD.column_name, OLD.column_value, NEW.column_value, 'UPDATE');
    ELSIF DELETING THEN
        INSERT INTO audit_log (table_name, column_name, old_value, operation)
        VALUES (OLD.table_name, OLD.column_name, OLD.column_value, 'DELETE');
    END IF;
END $$

DELIMITER ;
</pre>
</code>