/**
 * Student Correction Module JavaScript
 * Handles student data updates, UI interactions, and table functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Reference to important elements
    const topScrollContainer = document.querySelector('.top-scroll-container');
    const tableScrollContainer = document.querySelector('.table-scroll-container');
    const topScrollContent = document.querySelector('.top-scroll-content');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const alertContainer = document.getElementById('alertContainer');
    const table = document.querySelector('.table');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    /**
     * Set the width of the top scroll content to match the table width
     */
    function setTopScrollWidth() {
        if (tableScrollContainer && tableScrollContainer.querySelector('table')) {
            const tableWidth = tableScrollContainer.querySelector('table').offsetWidth;
            topScrollContent.style.width = tableWidth + 'px';
        }
    }

    // Initialize scroll containers and sync
    if (topScrollContainer && tableScrollContainer && topScrollContent) {
        setTopScrollWidth();
        window.addEventListener('resize', setTopScrollWidth);

        // Synchronize scrolling between the two containers
        topScrollContainer.addEventListener('scroll', function() {
            tableScrollContainer.scrollLeft = topScrollContainer.scrollLeft;
        });

        tableScrollContainer.addEventListener('scroll', function() {
            topScrollContainer.scrollLeft = tableScrollContainer.scrollLeft;
        });
    }

    /**
     * Show loading spinner
     */
    function showSpinner() {
        loadingSpinner.style.display = 'flex';
    }

    /**
     * Hide loading spinner
     */
    function hideSpinner() {
        loadingSpinner.style.display = 'none';
    }

    /**
     * Show alert message
     * @param {string} message - Message to display
     * @param {string} type - Alert type (success, danger, warning, info)
     */
    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show fade-out`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.appendChild(alertDiv);

        // Remove alert after animation
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    /**
     * Get all form data for a student
     * @param {string} studentPen - Student PEN ID
     * @returns {Object|null} - Student data object or null if error
     */
    function getStudentData(studentPen) {
        // Find the row with this ID
        const row = document.getElementById(`row-${studentPen}`);
        if (!row) {
            console.error(`Row not found for student PEN: ${studentPen}`);
            return null;
        }

        try {
            // Find all the fields in this row using attribute selectors
            const data = {
                student_pen: studentPen,
                student_name: row.querySelector(`input[name^="sname"][data-pen="${studentPen}"]`).value,
                father_name: row.querySelector(`input[name^="fth"][data-pen="${studentPen}"]`).value,
                mother_name: row.querySelector(`input[name^="mth"][data-pen="${studentPen}"]`).value,
                gender: row.querySelector(`select[name^="gend"][data-pen="${studentPen}"]`).value,
                dob: row.querySelector(`input[name^="dob"][data-pen="${studentPen}"]`).value,
                mobile: row.querySelector(`input[name^="mob"][data-pen="${studentPen}"]`).value,
                cwsn: row.querySelector(`select[name^="cws"][data-pen="${studentPen}"]`).value,
                aadhaar: row.querySelector(`input[name^="aadhaar"][data-pen="${studentPen}"]`).value || '',
                apaar: row.querySelector(`input[name^="apaar"][data-pen="${studentPen}"]`).value || '',
                nationality: row.querySelector(`select[name^="nationality"][data-pen="${studentPen}"]`).value,
                remark: row.querySelector(`input[name^="remark"][data-pen="${studentPen}"]`).value || '',
                category: row.querySelector(`select[name^="cat"][data-pen="${studentPen}"]`).value,
                class: row.querySelector(`select[name^="prcls"][data-pen="${studentPen}"]`).value,
                section: row.querySelector(`select[name^="sec"][data-pen="${studentPen}"]`).value,
                status: row.querySelector(`select[name^="status"][data-pen="${studentPen}"]`).value
            };

            return data;
        } catch (error) {
            console.error(`Error collecting data for student ${studentPen}:`, error);
            return null;
        }
    }

    // Handle individual student update
    const saveButtons = document.querySelectorAll('.save-student-data');
    if (saveButtons.length > 0) {
        saveButtons.forEach(button => {
            button.addEventListener('click', function() {
                const studentPen = this.getAttribute('data-pen');
                const studentData = getStudentData(studentPen);
                if (!studentData) return;

                showSpinner();

                // Get CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // AJAX request to save student data using fetch
                fetch('/student/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        action: 'save',
                        data: studentData
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        hideSpinner();
                        if (data.success) {
                            showAlert('Student data updated successfully!');

                            // Update cell background color based on status
                            const cells = document.querySelectorAll(`#row-${studentPen} td`);
                            cells.forEach(cell => {
                                if (studentData.status === 'E') {
                                    cell.classList.remove('not-filled');
                                    cell.classList.add('filled');
                                } else {
                                    cell.classList.remove('filled');
                                    cell.classList.add('not-filled');
                                }
                            });
                        } else {
                            showAlert('Error: ' + (data.message || 'Failed to update student data'), 'danger');
                        }
                    })
                    .catch(error => {
                        hideSpinner();
                        showAlert('Error: ' + error.message, 'danger');
                        console.error('Error:', error);
                    });
            });
        });
    }

    // "Update All" button handler
    if (document.getElementById('updateAllBtn')) {
        document.getElementById('updateAllBtn').addEventListener('click', function() {
            // Find all student rows
            const studentRows = document.querySelectorAll('.student-row');

            if (studentRows.length === 0) {
                showAlert('No student data available', 'warning');
                return;
            }

            // Create an array to store all student data
            const allStudentData = [];

            // Loop through each student row and collect data
            studentRows.forEach(row => {
                // Get the student PEN from the row ID
                const studentPen = row.id.replace('row-', '');
                if (!studentPen) {
                    console.error('Could not determine student PEN for row:', row);
                    return; // Skip this row
                }

                // Collect data for this student
                try {
                    const studentData = getStudentData(studentPen);
                    if (studentData) {
                        allStudentData.push(studentData);
                    }
                } catch (error) {
                    console.error('Error collecting data for student ' + studentPen + ':', error);
                }
            });

            // If we couldn't collect any data, show an error
            if (allStudentData.length === 0) {
                showAlert('Could not collect student data', 'warning');
                return;
            }

            // Show a progress indicator
            showSpinner();
            console.log('Updating data for ' + allStudentData.length + ' students');

            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send the AJAX request
            fetch('/student/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    action: 'updateAll',
                    data: allStudentData
                })
            })
                .then(response => response.json())
                .then(data => {
                    hideSpinner();
                    if (data.success) {
                        showAlert('All student data updated successfully!');

                        // Update all cell background colors based on status
                        studentRows.forEach(row => {
                            const pen = row.id.replace('row-', '');
                            // Find the status dropdown for this row
                            const statusDropdown = row.querySelector(`select[name^="status"][data-pen="${pen}"]`);
                            if (statusDropdown) {
                                const status = statusDropdown.value;
                                const cells = row.querySelectorAll('td');

                                cells.forEach(cell => {
                                    if (status === 'E') {
                                        cell.classList.remove('not-filled');
                                        cell.classList.add('filled');
                                    } else {
                                        cell.classList.remove('filled');
                                        cell.classList.add('not-filled');
                                    }
                                });
                            }
                        });
                    } else {
                        showAlert('Error: ' + (data.message || 'Failed to update all student data'), 'danger');
                    }
                })
                .catch(error => {
                    hideSpinner();
                    showAlert('Error: ' + error.message, 'danger');
                    console.error('Error:', error);
                });
        });
    }

    // Handle column toggles if table exists
    if (table && dropdownMenu) {
        // Get all table headers
        const headers = Array.from(table.querySelectorAll('thead th'));

        // Create checkboxes for each column
        headers.forEach((header, index) => {
            // Create list item
            const li = document.createElement('li');
            li.className = 'column-toggle-item';

            // Create checkbox wrapper with proper styling
            const checkboxWrapper = document.createElement('div');
            checkboxWrapper.className = 'form-check';

            // Create checkbox input
            const checkbox = document.createElement('input');
            checkbox.className = 'form-check-input';
            checkbox.type = 'checkbox';
            checkbox.id = `column-toggle-${index}`;
            checkbox.checked = true; // All columns visible by default

            // Create label for the checkbox
            const label = document.createElement('label');
            label.className = 'form-check-label';
            label.htmlFor = `column-toggle-${index}`;
            label.textContent = header.textContent.trim();

            // Handle checkbox change event
            checkbox.addEventListener('change', function() {
                toggleColumn(index, this.checked);
            });

            // Assemble the checkbox item
            checkboxWrapper.appendChild(checkbox);
            checkboxWrapper.appendChild(label);
            li.appendChild(checkboxWrapper);
            dropdownMenu.appendChild(li);
        });

        // Toggle column visibility function
        function toggleColumn(columnIndex, visible) {
            // Toggle visibility for header (th)
            const header = table.querySelector(`thead tr th:nth-child(${columnIndex + 1})`);
            if (header) {
                header.style.display = visible ? '' : 'none';
            }

            // Toggle visibility for all cells in that column (td)
            const cells = table.querySelectorAll(`tbody tr td:nth-child(${columnIndex + 1})`);
            cells.forEach(cell => {
                cell.style.display = visible ? '' : 'none';
            });

            // Also update the width of the top-scroll-content to match new table width
            setTopScrollWidth();
        }

        // Add "Select All" and "Deselect All" buttons
        const selectAllLi = document.createElement('li');
        selectAllLi.className = 'dropdown-item-text';

        const buttonGroup = document.createElement('div');
        buttonGroup.className = 'btn-group w-100 mt-2';

        const selectAllBtn = document.createElement('button');
        selectAllBtn.className = 'btn btn-sm btn-outline-primary';
        selectAllBtn.textContent = 'Select All';
        selectAllBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent dropdown from closing
            document.querySelectorAll('.column-toggle-item input').forEach((checkbox, index) => {
                checkbox.checked = true;
                toggleColumn(index, true);
            });
        });

        const deselectAllBtn = document.createElement('button');
        deselectAllBtn.className = 'btn btn-sm btn-outline-secondary';
        deselectAllBtn.textContent = 'Deselect All';
        deselectAllBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent dropdown from closing
            document.querySelectorAll('.column-toggle-item input').forEach((checkbox, index) => {
                checkbox.checked = false;
                toggleColumn(index, false);
            });
        });

        buttonGroup.appendChild(selectAllBtn);
        buttonGroup.appendChild(deselectAllBtn);
        selectAllLi.appendChild(buttonGroup);

        // Add select/deselect buttons at the top of the menu
        dropdownMenu.insertBefore(selectAllLi, dropdownMenu.firstChild);

        // Add divider after buttons
        const divider = document.createElement('li');
        divider.innerHTML = '<hr class="dropdown-divider">';
        dropdownMenu.insertBefore(divider, selectAllLi.nextSibling);

        // Handle dropdown click events to keep it open
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation(); // This keeps the dropdown open when clicking inside it
        });
    }

    // Input validation for Aadhaar number (numeric only)
    const aadhaarInputs = document.querySelectorAll('input[name^="aadhaar"]');
    aadhaarInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }
        });
    });

    // Input validation for mobile number (numeric only)
    const mobileInputs = document.querySelectorAll('input[name^="mob"]');
    mobileInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });
    });
});
