document.addEventListener('DOMContentLoaded', () => {
    const programTableBody = document.getElementById('programTableBody');
    const addProgramModal = document.getElementById('addProgramModal');
    const programModalTitle = document.getElementById('programModalTitle');
    const addProgramForm = document.getElementById('addProgramForm');
    const programUniversitySelect = document.getElementById('programUniversitySelect');
    const programFacultySelect = document.getElementById('programFacultySelect');
    const programSearchInput = document.getElementById('programSearchInput');

    let editingProgramId = null; 
    let programsData = []; 

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
    const fetchAndPopulateUniversities = async () => {
        programUniversitySelect.innerHTML = '<option value="">Select University</option>';
        try {
            const response = await fetch('../php/program.php?action=fetch_universities');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const universities = await response.json();
            if (!Array.isArray(universities)) throw new Error('Response is not an array');
            universities.forEach(uni => {
                const option = document.createElement('option');
                option.value = uni.university_id;
                option.textContent = uni.university_name;
                programUniversitySelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching universities:', error);
            showNotification('Failed to load universities for selection.', 'error');
        }
    };

    // --- Fetch and Populate Faculties Dropdown (no longer filtered by university) ---
    const fetchFacultiesForDropdown = async () => {
        programFacultySelect.innerHTML = '<option value="">Select Faculty</option>';
        try {
            // Note: The `university_id` is no longer used in the request URL
            const response = await fetch(`../php/program.php?action=fetch_faculties`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const faculties = await response.json();

            if (!Array.isArray(faculties)) {
                 console.error('Response is not an array:', faculties);
                 throw new Error('Response is not an array');
            }
            
            if (faculties.length === 0) {
                 programFacultySelect.innerHTML = '<option value="">No faculties found</option>';
            } else {
                 faculties.forEach(fac => {
                    const option = document.createElement('option');
                    option.value = fac.faculty_id;
                    option.textContent = fac.faculty_name;
                    programFacultySelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error fetching faculties for dropdown:', error);
            showNotification('Failed to load faculties for selection.', 'error');
        }
    };

    // --- Fetch Programs ---
    const fetchPrograms = async () => {
        try {
            const response = await fetch('../php/program.php');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            programsData = await response.json();
            if (!Array.isArray(programsData)) throw new Error('Response is not an array');
            displayPrograms(programsData);
        } catch (error) {
            console.error('Error fetching programs:', error);
            showNotification('Failed to load programs.', 'error');
        }
    };

    const displayPrograms = (programs) => {
        programTableBody.innerHTML = ''; 
        if (programs.length === 0) {
            programTableBody.innerHTML = '<tr><td colspan="6" class="text-center p-4 text-gray-500">No programs found.</td></tr>';
            return;
        }

        programs.forEach((prog, index) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="p-3 border">${index + 1}</td>
                <td class="p-3 border">${prog.program_name}</td>
                <td class="p-3 border">${prog.university_name || 'N/A'}</td>
                <td class="p-3 border">${prog.faculty_name || 'N/A'}</td>
                <td class="p-3 border max-w-xs overflow-hidden text-ellipsis whitespace-nowrap">${prog.description || 'N/A'}</td>
                <td class="p-3 border">
                    <button class="edit-btn bg-yellow-500 text-white px-3 py-1 rounded mr-2 hover:bg-yellow-600"
                            data-id="${prog.program_id}" data-university-id="${prog.university_id}" data-faculty-id="${prog.faculty_id}"
                            data-program-name="${prog.program_name}" data-description="${prog.description || ''}">Edit</button>
                    <button class="delete-btn bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                            data-id="${prog.program_id}" data-program-name="${prog.program_name}">Delete</button>
                </td>
            `;
            programTableBody.appendChild(row);
        });

        attachButtonListeners();
    };

    const attachButtonListeners = () => {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.onclick = async (e) => {
                editingProgramId = e.target.dataset.id;
                programModalTitle.textContent = 'Edit Program';
                
                const universityId = e.target.dataset.universityId;
                const facultyId = e.target.dataset.facultyId;

                await fetchAndPopulateUniversities();
                programUniversitySelect.value = universityId;

                // Call the new function that doesn't use a university ID
                await fetchFacultiesForDropdown();
                programFacultySelect.value = facultyId;

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
            const result = await response.json();

            if (result.status === 'success') {
                showNotification(result.message, 'success');
            } else if (result.status === 'duplicate') {
                showNotification(result.message, 'error');
            } else {
                showNotification(`Operation failed: ${result.message}`, 'error');
            }

            addProgramModal.classList.add('hidden');
            resetProgramForm();
            fetchPrograms();

        } catch (error) {
            console.error('Error submitting form:', error);
            showNotification('An error occurred while saving the program.', 'error');
        }
    });

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
                fetchPrograms();
            } else {
                showNotification(`Deletion failed: ${result.message}`, 'error');
            }
        } catch (error) {
            console.error('Error deleting program:', error);
            showNotification('An error occurred while deleting the program.', 'error');
        }
    };

    const resetProgramForm = () => {
        addProgramForm.reset();
        editingProgramId = null;
        programModalTitle.textContent = 'Add New Program';
        programUniversitySelect.value = '';
        programFacultySelect.innerHTML = '<option value="">Select Faculty</option>';
    };

    programSearchInput.addEventListener('input', () => {
        const searchTerm = programSearchInput.value.toLowerCase();
        const filteredPrograms = programsData.filter(prog => 
            (prog.program_name && prog.program_name.toLowerCase().includes(searchTerm)) ||
            (prog.university_name && prog.university_name.toLowerCase().includes(searchTerm)) ||
            (prog.faculty_name && prog.faculty_name.toLowerCase().includes(searchTerm))
        );
        displayPrograms(filteredPrograms);
    });

    programUniversitySelect.addEventListener('change', (e) => {
        // This event listener now calls the function without a parameter
        fetchFacultiesForDropdown();
    });

    fetchPrograms();
    
    document.querySelector('button[onclick*="addProgramModal"]').addEventListener('click', () => {
        resetProgramForm();
        fetchAndPopulateUniversities();
        // Also call the fetch faculties function when the modal opens
        fetchFacultiesForDropdown();
    });
});