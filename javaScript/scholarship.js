let editingScholarshipId = null;

function loadScholarships() {
  fetch("../php/load_scholarships.php")
    .then(res => res.text())
    .then(data => {
      const table = document.getElementById("scholarshipTable");
      table.innerHTML = data;
      attachScholarshipEventListeners();
    });
}

function submitScholarshipForm(event) {
  event.preventDefault();

  const form = document.getElementById("addScholarshipForm");
  const formData = new FormData(form);

  if (editingScholarshipId) {
    formData.append("action", "edit");
    formData.append("id", editingScholarshipId);
  } else {
    formData.append("action", "add");
  }

  fetch("../php/scholarship.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.text())
    .then(data => {
      console.log("Server response:", data); // ✅ Debug line added here

      if (data.trim() === "added" || data.trim() === "edited") {
        document.getElementById("addModal").classList.add("hidden");
        form.reset();
        editingScholarshipId = null;
        loadScholarships();
      } else {
        alert("Error: " + data);
      }
    });
}

function attachScholarshipEventListeners() {
  // Delete
  document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", e => {
      const row = e.target.closest("tr");
      const id = row.dataset.id;

      if (!confirm("Are you sure to delete this scholarship?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("id", id);

      fetch("../php/scholarship.php", {
        method: "POST",
        body: formData
      })
        .then(res => res.text())
        .then(data => {
          if (data.trim() === "deleted") {
            loadScholarships();
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
      editingScholarshipId = row.dataset.id;

      const form = document.getElementById("addScholarshipForm");
      form.scholarship_name.value = row.querySelector(".scholarship-name").textContent;
      form.province.value = row.querySelector(".scholarship-province").textContent;
      form.gpa.value = row.querySelector(".scholarship-gpa").textContent;
      form.degree_level.value = row.querySelector(".scholarship-degree").textContent;
      form.scholarship_type.value = row.querySelector(".scholarship-type").textContent;
      form.fields_of_study.value = row.querySelector(".scholarship-fields").textContent;
      form.college_type.value = row.querySelector(".scholarship-college").textContent;
      form.description.value = row.dataset.description || "";

      document.getElementById("addModal").classList.remove("hidden");
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  loadScholarships();
  document.getElementById("addScholarshipForm").addEventListener("submit", submitScholarshipForm);
});
