document.addEventListener('DOMContentLoaded', () => {
    const universityTableBody = document.getElementById('universityTableBody');
    const universitySearchInput = document.getElementById('universitySearchInput');
    const addUniversityBtn = document.getElementById('addUniversityBtn');
    const backToListBtn = document.getElementById('backToListBtn');
    const universityListSection = document.getElementById('universityListSection');
    const universityFormSection = document.getElementById('universityFormSection');
    const universityForm = document.getElementById('universityForm');
    const formTitle = document.getElementById('formTitle');
    const formAction = document.getElementById('formAction');
    const universityIdInput = document.getElementById('universityId');
    const currentImageInput = document.getElementById('currentImage');
    const currentImageDisplay = document.getElementById('currentImageDisplay');
    const imageHelpText = document.getElementById('imageHelpText');
    const mainTitle = document.getElementById('mainTitle'); // Main page title

    // Base URL for your PHP API endpoint
    // This path assumes:
    // - university-management.html is in 'yourproject/html/'
    // - university-management.php is in 'yourproject/php/'
    // So, from html/ to php/, it's one step up (..) then down into php/
    const API_URL = '../php/university-management.php';

    // --- Custom Modal Elements ---
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

    const showConfirmation = (message, onConfirmCallback, onCancelCallback) => {
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
            if (onConfirmCallback) onConfirmCallback();
        };
        confirmationModal.querySelector('#cancelBtn').onclick = () => {
            confirmationModal.remove();
            confirmationModal = null;
            if (onCancelCallback) onCancelCallback();
        };
    };

    // --- View Management Functions ---
    const showList = () => {
        universityListSection.classList.remove('hidden');
        universityFormSection.classList.add('hidden');
        mainTitle.textContent = 'Manage Universities';
        fetchUniversities(); // Refresh list when returning to it
    };

    const showForm = (action, university = null) => {
        universityListSection.classList.add('hidden');
        universityFormSection.classList.remove('hidden');
        universityForm.reset(); // Clear form fields

        if (action === 'add') {
            formTitle.textContent = 'Add University';
            mainTitle.textContent = 'Add University';
            formAction.value = 'add';
            universityIdInput.value = '';
            currentImageInput.value = '';
            currentImageDisplay.innerHTML = ''; // Clear image display
            imageHelpText.textContent = 'Upload image (optional)';
        } else if (action === 'edit' && university) {
            formTitle.textContent = 'Edit University';
            mainTitle.textContent = 'Edit University';
            formAction.value = 'edit';
            universityIdInput.value = university.university_id;
            currentImageInput.value = university.image; // Store current image name

            // Populate form fields
            universityForm.elements['university_name'].value = university.university_name;
            universityForm.elements['founded'].value = university.founded;
            universityForm.elements['website'].value = university.website;
            universityForm.elements['phone'].value = university.phone;
            universityForm.elements['campus'].value = university.campus;
            universityForm.elements['hours'].value = university.hours;
            universityForm.elements['details'].value = university.details;

            // Display current image
            if (university.image && university.image !== 'default_university.png') {
                // Path to images from the HTML file's perspective
                currentImageDisplay.innerHTML = `<img src="../../images/${university.image}" height="80" alt="Current University Image" class="mb-2">`;
                imageHelpText.textContent = 'Current image. Upload new to change.';
            } else {
                currentImageDisplay.innerHTML = '';
                imageHelpText.textContent = 'No current image. Upload one (optional).';
            }
        }
    };

    // --- Fetch and Display Universities ---
    const fetchUniversities = async () => {
        try {
            const response = await fetch(API_URL, { method: 'GET' });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();

            if (result.status === 'success') {
                displayUniversities(result.data);
            } else {
                showNotification(`Failed to load universities: ${result.message}`, 'error');
                universityTableBody.innerHTML = '<tr><td colspan="11" class="text-center p-4 text-gray-500">Failed to load universities.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching universities:', error);
            showNotification('An error occurred while fetching universities.', 'error');
            universityTableBody.innerHTML = '<tr><td colspan="11" class="text-center p-4 text-gray-500">Error loading data.</td></tr>';
        }
    };

    const displayUniversities = (universities) => {
        universityTableBody.innerHTML = ''; // Clear existing rows
        if (universities.length === 0) {
            universityTableBody.innerHTML = '<tr><td colspan="11" class="text-center p-4 text-gray-500">No universities found.</td></tr>';
            return;
        }

        universities.forEach((uni, index) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="p-3 border">${index + 1}</td>
                <td class="p-3 border">${uni.university_name}</td>
                <td class="p-3 border"><img src="../../images/${uni.image}" height="40" alt="University Image"></td>
                <td class="p-3 border">${uni.founded}</td>
                <td class="p-3 border"><a href="${uni.website}" target="_blank" class="text-blue-600 hover:underline">${uni.website}</a></td>
                <td class="p-3 border">${uni.phone}</td>
                <td class="p-3 border">${uni.campus}</td>
                <td class="p-3 border">${uni.hours}</td>
                <td class="p-3 border max-w-xs overflow-hidden text-ellipsis whitespace-nowrap">${uni.details.substring(0, 50)}...</td>
                <td class="p-3 border">${uni.created_at}</td>
                <td class="p-3 border">
                    <button class="edit-btn bg-yellow-500 text-white px-3 py-1 rounded mr-2 hover:bg-yellow-600" data-id="${uni.university_id}">Edit</button>
                    <button class="delete-btn bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700" data-id="${uni.university_id}" data-name="${uni.university_name}">Delete</button>
                </td>
            `;
            universityTableBody.appendChild(row);
        });

        // Attach event listeners to new buttons
        attachButtonListeners();
    };

    const attachButtonListeners = () => {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.onclick = async (e) => {
                const id = e.target.dataset.id;
                try {
                    // Fetch single university data from the API
                    const response = await fetch(`${API_URL}?action=fetch_single&id=${id}`);
                    if (!response.ok) throw new Error('Failed to fetch university for edit.');
                    const result = await response.json();
                    if (result.status === 'success' && result.data) {
                        showForm('edit', result.data);
                    } else {
                        showNotification(result.message || 'Could not load university for editing.', 'error');
                    }
                } catch (error) {
                    console.error('Error fetching single university:', error);
                    showNotification('Error loading university details for editing.', 'error');
                }
            };
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.onclick = (e) => {
                const id = e.target.dataset.id;
                const name = e.target.dataset.name;
                showConfirmation(`Are you sure you want to delete "${name}"? This action cannot be undone.`, () => {
                    handleDeleteUniversity(id);
                });
            };
        });
    };

    // --- Handle Form Submission (Add/Edit) ---
    universityForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(universityForm); // Automatically collects all form fields, including file

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData,
            });
            const result = await response.json(); // PHP returns JSON

            if (result.status === 'success') {
                showNotification(result.message, 'success');
                showList(); // Go back to list view and refresh
            } else if (result.status === 'duplicate') {
                showNotification(result.message, 'error');
            } else {
                showNotification(`Operation failed: ${result.message}`, 'error');
            }

        } catch (error) {
            console.error('Error submitting form:', error);
            showNotification('An error occurred while saving the university.', 'error');
        }
    });

    // --- Handle Delete University ---
    const handleDeleteUniversity = async (id) => {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('university_id', id);

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();

            if (result.status === 'success') {
                showNotification(result.message, 'success');
                fetchUniversities(); // Refresh the table
            } else {
                showNotification(`Deletion failed: ${result.message}`, 'error');
            }
        } catch (error) {
            console.error('Error deleting university:', error);
            showNotification('An error occurred while deleting the university.', 'error');
        }
    };

    // --- Search Functionality ---
    universitySearchInput.addEventListener('input', () => {
        const searchTerm = universitySearchInput.value.toLowerCase();
        const rows = universityTableBody.querySelectorAll('tr');
        rows.forEach(row => {
            // Assuming university name is in the 2nd td (index 1)
            const universityName = row.children[1].textContent.toLowerCase();
            if (universityName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // --- Event Listeners for UI Buttons ---
    addUniversityBtn.addEventListener('click', () => showForm('add'));
    backToListBtn.addEventListener('click', showList);

    // Initial load: Show list view
    showList();
});

//***************************** */