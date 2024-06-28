<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Table</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container">
    <h1 class="mt-5">User Table</h1>
    <button id="addColumn" class="btn btn-primary">Add Custom Column</button>
    <table class="table table-bordered mt-3" id="userTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Nickname</th>
                <th>Mobile Number</th>
                <th>Email</th>
                <th>Role</th>
                <th>Address</th>
                <th>Gender</th>
                <th>Profile Image</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be inserted here -->
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    var userId = new URLSearchParams(window.location.search).get('userId') || 0;

    function fetchUserData() {
        $.ajax({
            url: 'includes/fetch_users.php',
            method: 'GET',
            data: { userId: userId },
            dataType: 'json',
            success: function(data) {
                var thead = $('#userTable thead tr');
                var tbody = $('#userTable tbody');
                tbody.empty();
                
                // Clear custom columns
                thead.find('.custom-column').remove();
                
                $.each(data.customColumns, function(i, column) {
                    thead.append('<th class="custom-column">' + column.column_name + '</th>');
                });
                
                $.each(data.users, function(index, user) {
                    var row = '<tr>';
                    row += '<td>' + user.name + '</td>';
                    row += '<td>' + user.nickname + '</td>';
                    row += '<td>' + user.mobile_number + '</td>';
                    row += '<td>' + user.email + '</td>';
                    row += '<td>' + user.role + '</td>';
                    row += '<td>' + user.address + '</td>';
                    row += '<td>' + user.gender + '</td>';
                    row += '<td><img src="' + user.profile_image + '" alt="Profile Image" width="50" height="50"></td>';
                    $.each(data.customColumns, function(i, column) {
                        row += '<td class="custom-column" data-column-name="' + column.column_name + '">' + (user[column.column_name] || '') + '</td>';
                    });
                    row += '</tr>';
                    tbody.append(row);
                });
            }
        });
    }

    fetchUserData();

    $('#addColumn').click(function() {
        var columnName = prompt("Enter column name:");
        if (columnName) {
            var columnCount = $('#userTable thead th').length;
            if (columnCount >= 20) {
                alert("You can't add more than 20 columns.");
                return;
            }

            $.ajax({
                url: 'includes/add_column.php',
                method: 'POST',
                data: { userId: userId, columnName: columnName },
                success: function(response) {
                    if (response == 'exists') {
                        alert("Column with this name already exists.");
                    } else if (response == 'success') {
                        fetchUserData();
                    }
                }
            });
        }
    });
});
</script>
</body>
</html>
