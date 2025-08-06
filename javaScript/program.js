document.addEventListener('DOMContentLoaded', () => {
    const programTableBody = document.getElementById('programTableBody');
    const addProgramModal = document.getElementById('addProgramModal');
    const programModalTitle = document.getElementById('programModalTitle');
    const addProgramForm = document.getElementById('addProgramForm');
    const programUniversitySelect = document.getElementById('programUniversitySelect');
    const programSearchInput = document.getElementById('programSearchInput');

    let editingProgramId = null; // To store the ID of the program being edited

    // --- Custom Modal Elements (reused from university.js logic) ---
    let notificationModal = null;
    let confirmationModal = null;

    const showNotification = (message, type) => {
        if (notificationModal) {
            notificationModal.remove();
        }
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const borderColor = type === 'success' ? 'border-green-700' : 'border-red-700';

        notificationModal = document.createElement('div');
        notificationModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4';
        notificationModal.innerHTML = `
            <div class="${bgColor} text-white p-6 rounded-lg shadow-xl border-2 ${borderColor} max-w-sm w-full text-center">
                <p class="text-lg font-semibold mb-4">${message}</p>
                <button class="bg-white text-gray-800 px-6 py-2 rounded-full font-semibold hover:bg-gray-200 transition-colors duration-200">
                    OK
                </button>
            </div>
        `;
        document.body.appendChild(notificationModal);
        notificationModal.querySelector('button').onclick = () => {
            notificationModal.remove();
            notificationModal = null;
        };
    };

    const showConfirmation = (message, onConfirmCallback) => {
        if (confirmationModal) {
            confirmationModal.remove();
        }
        confirmationModal = document.createElement('div');
        confirmationModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4';
        confirmationModal.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow-xl border-2 border-gray-300 max-w-sm w-full text-center">
                <p class="text-lg font-semibold text-gray-800 mb-6">${message}</p>
                <div class="flex justify-center space-x-4">
                    <button id="confirmBtn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full font-semibold transition-colors duration-200">
                        Confirm
                    </button>
                    <button id="cancelBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-full font-semibold transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(confirmationModal);

        confirmationModal.querySelector('#confirmBtn').onclick = () => {
            confirmationModal.remove();
            confirmationModal = null;
            onConfirmCallback();
        };
        confirmationModal.querySelector('#cancelBtn').onclick = () => {
            confirmationModal.remove();
            confirmationModal = null;
        };
    };

    // --- Fetch and Populate Universities Dropdown ---
    const fetchUniversitiesForDropdown = async () => {
    try {
        const response = await fetch('../php/program.php?action=fetch_universities');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const universities = await response.json();
            console.log('Universities fetched:', universities);
        if (!Array.isArray(universities)) throw new Error('Response is not an array');
        programUniversitySelect.innerHTML = '<option value="">Select University</option>';
        universities.forEach(uni => {
            const option = document.createElement('option');
            option.value = uni.University_ID;
            option.textContent = uni.University_Name;
            programUniversitySelect.appendChild(option);
        });
    } catch (error) {
            console.error('Error fetching universities for dropdown:', error);
        showNotification('Failed to load universities for selection.', 'error');
    }
};

const fetchPrograms = async () => {
    try {
        const response = await fetch('../php/program.php');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const programs = await response.json();
          console.log('Programs fetched:', programs);
        if (!Array.isArray(programs)) throw new Error('Response is not an array');
        displayPrograms(programs);
    } catch (error) {
          console.error('Error fetching programs:', error);
        showNotification('Failed to load programs.', 'error');
    }
};


    const displayPrograms = (programs) => {
        programTableBody.innerHTML = ''; // Clear existing rows
        if (programs.length === 0) {
            programTableBody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-gray-500">No programs found.</td></tr>';
            return;
        }

        programs.forEach((prog, index) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="p-3 border">${index + 1}</td>
                <td class="p-3 border">${prog.Program_Name}</td>
                <td class="p-3 border">${prog.University_Name || 'N/A'}</td>
                <td class="p-3 border max-w-xs overflow-hidden text-ellipsis whitespace-nowrap">${prog.Description || 'N/A'}</td>
                <td class="p-3 border">
                    <button class="edit-btn bg-yellow-500 text-white px-3 py-1 rounded mr-2 hover:bg-yellow-600"
                            data-id="${prog.Program_ID}" data-university-id="${prog.University_ID}"
                            data-program-name="${prog.Program_Name}" data-description="${prog.Description}">Edit</button>
                    <button class="delete-btn bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                            data-id="${prog.Program_ID}" data-program-name="${prog.Program_Name}">Delete</button>
                </td>
            `;
            programTableBody.appendChild(row);
        });

        // Attach event listeners to new buttons
        attachButtonListeners();
    };

    const attachButtonListeners = () => {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.onclick = (e) => {
                editingProgramId = e.target.dataset.id;
                programModalTitle.textContent = 'Edit Program';
                addProgramForm.elements.university_id.value = e.target.dataset.universityId;
                addProgramForm.elements.program_name.value = e.target.dataset.programName;
                addProgramForm.elements.description.value = e.target.dataset.description;
                addProgramModal.classList.remove('hidden');
            };
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.onclick = (e) => {
                const id = e.target.dataset.id;
                const programName = e.target.dataset.programName;
                showConfirmation(`Are you sure you want to delete "${programName}"?`, () => {
                    handleDeleteProgram(id);
                });
            };
        });
    };

    // --- Handle Form Submission (Add/Edit) ---
    addProgramForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(addProgramForm);
        formData.append('action', editingProgramId ? 'edit' : 'add');
        if (editingProgramId) {
            formData.append('program_id', editingProgramId);
        }

        try {
            const response = await fetch('../php/program.php', {
                method: 'POST',
                body: formData,
            });
            const result = await response.json(); // PHP returns JSON

            if (result.status === 'success') {
                showNotification(result.message, 'success');
            } else if (result.status === 'duplicate') {
                showNotification(result.message, 'error');
            } else {
                showNotification(`Operation failed: ${result.message}`, 'error');
            }

            addProgramModal.classList.add('hidden');
            resetProgramForm(); // Reset form fields
            fetchPrograms(); // Refresh the table

        } catch (error) {
            console.error('Error submitting form:', error);
            showNotification('An error occurred while saving the program.', 'error');
        }
    });

    // --- Handle Delete Program ---
    const handleDeleteProgram = async (id) => {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('program_id', id);

        try {
            const response = await fetch('../php/program.php', {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();

            if (result.status === 'success') {
                showNotification(result.message, 'success');
                fetchPrograms(); // Refresh the table
            } else {
                showNotification(`Deletion failed: ${result.message}`, 'error');
            }
        } catch (error) {
            console.error('Error deleting program:', error);
            showNotification('An error occurred while deleting the program.', 'error');
        }
    };

    // --- Reset Form Function ---
    const resetProgramForm = () => {
        addProgramForm.reset();
        editingProgramId = null;
        programModalTitle.textContent = 'Add New Program';
        programUniversitySelect.value = ''; // Reset dropdown
    };

    // --- Search Functionality ---
    programSearchInput.addEventListener('input', () => {
        const searchTerm = programSearchInput.value.toLowerCase();
        const rows = programTableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const programName = row.children[1].textContent.toLowerCase();
            const universityName = row.children[2].textContent.toLowerCase(); // Search by university name too
            if (programName.includes(searchTerm) || universityName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Initial fetches when the page loads
    fetchUniversitiesForDropdown();
    fetchPrograms();
});
///******************** */
