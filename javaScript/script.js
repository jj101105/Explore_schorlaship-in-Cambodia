import { universityList } from './university-data.js';

// JavaScript for Sidebar Toggle
const menuIcon = document.getElementById("menu-icon");
const sidebar = document.getElementById("sidebar");
const closeBtn = document.getElementById("close-menu");

console.log(universityList); 

// Show sidebar when menu icon is clicked
menuIcon.addEventListener("click", function() {
  sidebar.classList.remove("hidden"); // Remove 'hidden' class to show sidebar
});

// Hide sidebar when close button is clicked
closeBtn.addEventListener("click", function() {
  sidebar.classList.add("hidden"); // Add 'hidden' class to hide sidebar
});
