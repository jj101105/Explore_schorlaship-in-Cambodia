let editingScholarshipId = null;

function loadScholarships() {
  fetch("../php/load_scholarships1.php")
    .then(res => res.text())
    .then(data => {
      const table = document.getElementById("scholarshipTable");
      table.innerHTML = data;
      attachScholarshipEventListeners();
    })
    .catch(err => {
      console.error("Error loading scholarships:", err);
      alert("Failed to load scholarships.");
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

  fetch("../php/scholarship1.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.text())
    .then(data => {
      console.log("Server response:", data); 

      if (data.trim() === "added" || data.trim() === "edited") {
        document.getElementById("addModal").classList.add("hidden");
        form.reset();
        editingScholarshipId = null;
        loadScholarships();
      } else {
        alert("Error: " + data);
      }
    })
    .catch(err => {
      console.error("Error submitting form:", err);
      alert("Failed to submit the form.");
    });
}

function attachScholarshipEventListeners() {
  // Delete buttons
  document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", e => {
      const row = e.target.closest("tr");
      const id = row.dataset.id;

      if (!confirm("Are you sure to delete this scholarship?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("id", id);

      fetch("../php/scholarship1.php", {  // fixed URL here
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
        })
        .catch(err => {
          console.error("Delete error:", err);
          alert("Delete failed due to a network error.");
        });
    });
  });

  // Edit buttons
  document.querySelectorAll(".edit-btn").forEach(button => {
    button.addEventListener("click", e => {
      const row = e.target.closest("tr");
      editingScholarshipId = row.dataset.id;

      const form = document.getElementById("addScholarshipForm");
      form.scholarship_name.value = row.querySelector(".scholarship-name").textContent.trim();
      form.province.value = row.querySelector(".scholarship-province").textContent.trim();
      form.gpa.value = row.querySelector(".scholarship-gpa").textContent.trim();
      form.degree_level.value = row.querySelector(".scholarship-degree").textContent.trim();
      form.scholarship_type.value = row.querySelector(".scholarship-type").textContent.trim();
      form.fields_of_study.value = row.querySelector(".scholarship-fields").textContent.trim();
      form.college_type.value = row.querySelector(".scholarship-college").textContent.trim();
      form.deadline.value = row.dataset.deadline || "";
      form.sponsor.value = row.dataset.sponsor || "";
      form.benefits.value = row.dataset.benefits || "";
      form.apply.value = row.dataset.apply || "";
      form.link.value = row.dataset.link || "";
      form.description.value = row.dataset.description || "";

      document.getElementById("addModal").classList.remove("hidden");
    });
  });
}

function openAddScholarshipForm() {
  editingScholarshipId = null;
  const form = document.getElementById("addScholarshipForm");
  form.reset();
  document.getElementById("addModal").classList.remove("hidden");
}

document.addEventListener("DOMContentLoaded", () => {
  loadScholarships();
  document.getElementById("addScholarshipForm").addEventListener("submit", submitScholarshipForm);

  // Optional: Search filter on scholarship name
  document.getElementById("searchInput").addEventListener("input", function() {
    const filter = this.value.toLowerCase();
    document.querySelectorAll("#scholarshipTable tr").forEach(row => {
      const name = row.querySelector(".scholarship-name").textContent.toLowerCase();
      row.style.display = name.includes(filter) ? "" : "none";
    });
  });
});
