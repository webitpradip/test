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
CREATE PROCEDURE create_audit_triggers()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE table_name VARCHAR(255);
    DECLARE cur CURSOR FOR 
        SELECT TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = 'your_database_name';

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO table_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET @trigger_sql = CONCAT(
            'CREATE TRIGGER audit_trigger_', table_name, ' AFTER INSERT OR UPDATE OR DELETE ON ', table_name,
            ' FOR EACH ROW BEGIN ',
                'IF INSERTING THEN ',
                    'INSERT INTO audit_log (table_name, column_name, new_value, operation) ',
                    'VALUES (NEW.table_name, NEW.column_name, NEW.column_value, ''INSERT''); ',
                'ELSIF UPDATING THEN ',
                     'INSERT INTO audit_log (table_name, column_name, old_value, new_value, operation) ',
                     'VALUES (OLD.table_name, OLD.column_name, OLD.column_value, NEW.column_value, ''UPDATE''); ',
                'ELSIF DELETING THEN ',
                    'INSERT INTO audit_log (table_name, column_name, old_value, operation) ',
                    'VALUES (OLD.table_name, OLD.column_name, OLD.column_value, ''DELETE''); ',
                'END IF;',
            'END $$'
        );

        PREPARE stmt FROM @trigger_sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END LOOP;

    CLOSE cur;
END $$
DELIMITER ;

CALL create_audit_triggers(); 
</pre>
</code>

<code>
    <pre>
    DELIMITER $$

CREATE PROCEDURE delete_audit_triggers()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE trigger_name VARCHAR(255);
    
    -- Cursor to select names of all triggers that were created for auditing
    DECLARE cur CURSOR FOR 
        SELECT TRIGGER_NAME
        FROM INFORMATION_SCHEMA.TRIGGERS 
        WHERE TRIGGER_SCHEMA = 'your_database_name'
        AND TRIGGER_NAME LIKE 'audit_trigger_%';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO trigger_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET @drop_sql = CONCAT('DROP TRIGGER IF EXISTS ', trigger_name, ';');

        PREPARE stmt FROM @drop_sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END LOOP;

    CLOSE cur;
END$$

DELIMITER ;

    </pre>
</code>

<code>
    <pre>
        CALL delete_audit_triggers();
    </pre>    
</code>