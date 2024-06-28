<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <div class="container my-5">
    <div class="row">
      <div class="col-12">
        <h1 class="mb-3">User List</h1>
      </div>
      <div class="col-12 d-flex align-items-center justify-content-between mb-3">
        <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            Show/Hide Column
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="showHideDropdown">
            <!-- here will appear all columns for show hide -->
          </ul>
        </div>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
          Add Column
        </button>

      </div>
    </div>
    <div class="col-12 table-responsive">
      <table id="user-table" class="table table-striped table-hover">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Nickname</th>
            <th scope="col">Mobile</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>
            <th scope="col">Address</th>
            <th scope="col">Gender</th>
            <th scope="col">Profile Image</th>
          </tr>
        </thead>
        <tbody id="user-table-body">
          <!-- here data will be show form the db -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- add column modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Add Column</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addColumnForm">
            <div class="mb-3">
              <label for="columnName" class="form-label">Column Name</label>
              <input type="text" class="form-control" id="columnName" name="columnName" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="addColumnBtn">Add</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    // Helper function to get query parameter
    function getQueryParam(param) {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(param);
    }

    document.addEventListener("DOMContentLoaded", function() {
      const userID = getQueryParam('userID');

      function fetchUsers() {
        fetch(`includes/fetchUser.php?userID=${userID}`)
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            const tableBody = document.getElementById("user-table-body");
            tableBody.innerHTML = "";

            if (Array.isArray(data.users)) {
              data.users.forEach((user, index) => {
                let row = document.createElement('tr');
                Object.values(user).forEach(value => {
                  if (Array.isArray(value)) {
                    value.forEach(column => {
                      let cell = document.createElement('td');
                      cell.textContent = column.name;
                      row.appendChild(cell);
                    });
                  } else {
                    let cell = document.createElement('td');
                    cell.textContent = value;
                    row.appendChild(cell);
                  }
                });
                tableBody.appendChild(row);
              });
            }

            if (Array.isArray(data.columns)) {
              data.columns.forEach(column => {
                const tableHead = document.getElementById("user-table").querySelector("thead tr");
                const newColumnHeader = document.createElement("th");
                newColumnHeader.textContent = column.name;
                tableHead.appendChild(newColumnHeader);

                const tableBodyRows = document.getElementById("user-table-body").querySelectorAll("tr");
                tableBodyRows.forEach(row => {
                  const newCell = document.createElement("td");
                  newCell.setAttribute("contenteditable", "true");
                  row.appendChild(newCell);
                });
              });
            }

            updateShowHideDropdown();
          })
          .catch(error => {
            console.error("Error fetching user data:", error);
          });
      }

      fetchUsers();


      // for Adding new column
      const addColumnBtn = document.getElementById("addColumnBtn");
      addColumnBtn.addEventListener("click", function() {
        const columnName = document.getElementById("columnName").value.trim();
        if (columnName !== "") {
          fetch("includes/addColumns.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json"
              },
              body: JSON.stringify({
                userID,
                columnName
              })
            })
            .then(response => response.json())
            .then(data => {
               

              if (Array.isArray(data.columns)) {
                data.columns.forEach(column => {
                  const tableHead = document.getElementById("user-table").querySelector("thead tr");
                  const newColumnHeader = document.createElement("th");
                  newColumnHeader.textContent = column.name;
                  tableHead.appendChild(newColumnHeader);

                  const tableBodyRows = document.getElementById("user-table-body").querySelectorAll("tr");
                  tableBodyRows.forEach(row => {
                    const newCell = document.createElement("td");
                    newCell.setAttribute("contenteditable", "true");
                    row.appendChild(newCell);
                    // Hide the column in the table if it's not visible
                    if (!column.is_visible) {
                      newColumnHeader.style.display = "none";
                      newCell.style.display = "none";
                    }
                  });
                });
              }
 
            })
            .catch(error => {
              console.error("Error adding column:", error);
            });
        }
      });

      // Update Show/Hide Column dropdown
      function updateShowHideDropdown() {
        const dropdownMenu = document.getElementById("showHideDropdown");
        dropdownMenu.innerHTML = "";
        const headers = document.getElementById("user-table").querySelectorAll("thead th");
        headers.forEach((header, index) => {
          const columnName = header.textContent.trim();
          const listItem = document.createElement("li");
          const label = document.createElement("label");
          label.className = "dropdown-item";
          const checkbox = document.createElement("input");
          checkbox.type = "checkbox";
          checkbox.className = "form-check-input";
          checkbox.value = index;
          checkbox.checked = true;

          // disabled the id
          if (columnName === "ID") {
            checkbox.disabled = true;
          }

          label.appendChild(checkbox);
          label.append(` ${columnName}`);
          listItem.appendChild(label);
          dropdownMenu.appendChild(listItem);
        });
        addShowHideListeners();
      }

      // Add event listeners to checkboxes
      function addShowHideListeners() {
        const checkboxes = document.querySelectorAll("#showHideDropdown input[type='checkbox']");
        checkboxes.forEach(checkbox => {
          checkbox.addEventListener("change", function() {
            const index = this.value;
            const table = document.getElementById("user-table");
            const header = table.querySelector(`thead th:nth-child(${parseInt(index) + 1})`);
            const cells = table.querySelectorAll(`tbody td:nth-child(${parseInt(index) + 1})`);
            if (this.checked) {
              header.style.display = "";
              cells.forEach(cell => cell.style.display = "");
            } else {
              header.style.display = "none";
              cells.forEach(cell => cell.style.display = "none");
            }
          });
        });
        fetchUsers();
      }

      updateShowHideDropdown();



      function updateColumnVisibility(columnName, isVisible) {
        fetch("includes/columShowHide.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              userID,
              columnName,
              isVisible
            })
          })
          .then(response => response.json())
          .then(data => {
            if (!data.success) {
              console.error("Failed to update column visibility.");
            }
          })
          .catch(error => {
            console.error("Error updating column visibility:", error);
          });
      }

      function addShowHideListeners() {
        const checkboxes = document.querySelectorAll("#showHideDropdown input[type='checkbox']");
        checkboxes.forEach(checkbox => {
          checkbox.addEventListener("change", function() {
            const index = this.value;
            const table = document.getElementById("user-table");
            const header = table.querySelector(`thead th:nth-child(${parseInt(index) + 1})`);
            const cells = table.querySelectorAll(`tbody td:nth-child(${parseInt(index) + 1})`);
            const columnName = header.textContent.trim();
            if (this.checked) {
              header.style.display = "";
              cells.forEach(cell => cell.style.display = "");
              updateColumnVisibility(columnName, true);
            } else {
              header.style.display = "none";
              cells.forEach(cell => cell.style.display = "none");
              updateColumnVisibility(columnName, false);
            }
          });
        });
      }

      // Call the function to add event listeners
      addShowHideListeners();

    });
  </script>
</body>

</html>