<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic MySQL Dropdowns</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
    </style>
</head>
<body>
    <?php require_once('menu.php') ?>
    <h1>Dynamic MySQL Dropdowns</h1>
    <table id="dynamic-table">
        <thead>
            <tr>
                <th>Table</th>
                <th>Fields</th>
                <th>First Join Table</th>
                <th>First Join Field</th>
                <th>Join Type</th>
                <th>Second Join Table</th>
                <th>Second Join Field</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select class="table-dropdown">
                        <option value="">Select Table</option>
                    </select>
                </td>
                <td>
                    <select class="fields-dropdown" multiple>
                    </select>
                </td>
                <td>
                    <select class="first-join-table">
                        <option value="">Select Table</option>
                    </select>
                </td>
                <td>
                    <select class="first-join-field">
                        <option value="">Select Field</option>
                    </select>
                </td>
                <td>
                    <select class="join-type">
                        <option value="">Select Join Type</option>
                        <option value="inner join">Inner Join</option>
                        <option value="left join">Left Join</option>
                        <option value="right join">Right Join</option>
                        
                    </select>
                </td>
                <td>
                    <select class="second-join-table">
                        <option value="">Select Table</option>
                    </select>
                </td>
                <td>
                    <select class="second-join-field">
                        <option value="">Select Field</option>
                    </select>
                </td>
                <td>
                    <button class="add-row">+</button>
                    <button class="remove-row">-</button>
                </td>
            </tr>
        </tbody>
    </table>
    <button id="generate-query">Generate Query</button>
    <p id="query-result"></p>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function fetchTables() {
                $.ajax({
                    url: 'get_tables.php',
                    method: 'GET',
                    success: function(data) {
                        let tables = JSON.parse(data);
                        $('.table-dropdown, .first-join-table, .second-join-table').each(function() {
                            let dropdown = $(this);
                            dropdown.empty().append('<option value="">Select Table</option>');
                            tables.forEach(function(table) {
                                dropdown.append('<option value="' + table + '">' + table + '</option>');
                            });
                        });
                    }
                });
            }

            function fetchFields(tableName, fieldDropdown) {
                $.ajax({
                    url: 'get_fields.php',
                    method: 'GET',
                    data: { table: tableName },
                    success: function(data) {
                        let fields = JSON.parse(data);
                        fieldDropdown.empty();
                        fields.forEach(function(field) {
                            fieldDropdown.append('<option value="' + field + '">' + field + '</option>');
                        });
                    }
                });
            }

            function addRow() {
                let newRow = $('#dynamic-table tbody tr:first').clone();
                newRow.find('select').val('');
                newRow.find('.fields-dropdown').empty();
                $('#dynamic-table tbody').append(newRow);
            }

            function removeRow(row) {
                if ($('#dynamic-table tbody tr').length > 1) {
                    row.remove();
                }
            }

            $('#dynamic-table').on('change', '.table-dropdown', function() {
                let tableName = $(this).val();
                let fieldDropdown = $(this).closest('tr').find('.fields-dropdown');
                if (tableName) {
                    fetchFields(tableName, fieldDropdown);
                } else {
                    fieldDropdown.empty();
                }
            });

            $('#dynamic-table').on('change', '.first-join-table, .second-join-table', function() {
                let tableName = $(this).val();
                let fieldDropdown = $(this).closest('td').next().find('select');
                if (tableName) {
                    fetchFields(tableName, fieldDropdown);
                } else {
                    fieldDropdown.empty();
                }
            });

            $('#dynamic-table').on('click', '.add-row', function() {
                addRow();
            });

            $('#dynamic-table').on('click', '.remove-row', function() {
                removeRow($(this).closest('tr'));
            });

            $('#generate-query').click(function() {
                let query = 'SELECT ';
                var joins = '';
                let selections = [];
                var joinTypes = [];
                var firstJoinTables = [];
                var firstJoinFields = [];
                var secondJoinTables = [];
                var secondJoinFields = [];
                $('#dynamic-table tbody tr').each(function() {
                    let table = $(this).find('.table-dropdown').val();
                    let fields = $(this).find('.fields-dropdown').val();
                    
                    if($(this).find('.first-join-table').val()){
                        firstJoinTables.push($(this).find('.first-join-table').val()) 
                    }

                    if($(this).find('.first-join-field').val()){
                        firstJoinFields.push($(this).find('.first-join-field').val()) 
                    }

                    if($(this).find('.join-type').val()){
                        joinTypes.push($(this).find('.join-type').val()) 
                    }
                    
                    if($(this).find('.second-join-table').val()){
                        secondJoinTables.push($(this).find('.second-join-table').val()) 
                    }

                    if($(this).find('.second-join-field').val()){
                        secondJoinFields.push($(this).find('.second-join-field').val()) 
                    }

                    if (table && fields) {
                        selections.push(fields.map(field => table + '.' + field).join(', '));
                    }
                });
                query += selections.join(', ') + ' FROM ';
                joins+= firstJoinTables[0]+' ';
                for(i in firstJoinTables){
                    joins+= ' ' +joinTypes[i]+' '+secondJoinTables[i] + ' on ' + firstJoinTables[i]+'.'+firstJoinFields[i]+' = '+secondJoinTables[i]+'.'+secondJoinFields[i]
                }            
                query += joins;
                $('#query-result').text(query);
            });

            fetchTables();
        });
    </script>
</body>
</html>
