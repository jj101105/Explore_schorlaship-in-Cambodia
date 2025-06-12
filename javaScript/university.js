 let editingUniversityId = null;

function loadUniversities() {
  fetch("../php/load_universities.php")
    .then(res => res.text())
    .then(data => {
      const table = document.getElementById("universityTable");
      table.innerHTML = data;
      attachEventListeners(); // bind edit/delete
    });
}

function submitUniversityForm(event) {
  event.preventDefault();

  const form = document.getElementById("addUniversityForm");
  const formData = new FormData(form);

  if (editingUniversityId) {
    formData.append("action", "edit");
    formData.append("id", editingUniversityId);
  } else {
    formData.append("action", "add");
  }

  fetch("../php/university.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.text())
    .then(data => {
      if (data.trim() === "added" || data.trim() === "edited") {
        document.getElementById("addUniversityModal").classList.add("hidden");
        form.reset();
        editingUniversityId = null;
        loadUniversities();
      } else {
        alert("Error: " + data);
      }
    });
}

function attachEventListeners() {
  // Delete
  document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", e => {
      const row = e.target.closest("tr");
      const id = row.dataset.id;

      if (!confirm("Are you sure to delete?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("id", id);

      fetch("../php/university.php", {
        method: "POST",
        body: formData
      })
        .then(res => res.text())
        .then(data => {
          if (data.trim() === "deleted") {
            loadUniversities();
          } else {
            alert("Delete failed: " + data);
          }
        });
    });
  });

  // Edit
  document.querySelectorAll(".edit-btn").forEach(button => {
    button.addEventListener("click", e => {
      const row = e.target.closest("tr");
      editingUniversityId = row.dataset.id;

      const name = row.querySelector(".university-name").textContent;
      const location = row.querySelector(".university-location").textContent;
      const type = row.querySelector(".university-type").textContent;
      const website = row.querySelector(".university-website a")?.textContent || "";

      const form = document.getElementById("addUniversityForm");
      form.name.value = name;
      form.location.value = location;
      form.type.value = type;
      form.website.value = website;

      document.getElementById("addUniversityModal").classList.remove("hidden");
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  loadUniversities();
  document.getElementById("addUniversityForm").addEventListener("submit", submitUniversityForm);
});     