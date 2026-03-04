const yearEl = document.getElementById("year");
if (yearEl) {
  yearEl.textContent = String(new Date().getFullYear());
}

const toggle = document.querySelector(".menu-toggle");
const nav = document.querySelector(".main-nav");
const menuModal = document.getElementById("menu-modal");
const openMenuButtons = document.querySelectorAll("[data-open-menu]");
const closeMenuButton = document.querySelector(".menu-modal-close");
const filterButtons = document.querySelectorAll("[data-menu-filter]");
const menuSections = document.querySelectorAll("[data-menu-category]");

if (toggle && nav) {
  toggle.addEventListener("click", () => {
    const open = nav.classList.toggle("open");
    toggle.setAttribute("aria-expanded", String(open));
  });
}

function openMenuModal() {
  if (!menuModal) return;
  menuModal.setAttribute("aria-hidden", "false");
  document.body.classList.add("modal-open");
  if (nav && toggle) {
    nav.classList.remove("open");
    toggle.setAttribute("aria-expanded", "false");
  }
}

function closeMenuModal() {
  if (!menuModal) return;
  menuModal.setAttribute("aria-hidden", "true");
  document.body.classList.remove("modal-open");
}

openMenuButtons.forEach((button) => {
  button.addEventListener("click", openMenuModal);
});

if (closeMenuButton) {
  closeMenuButton.addEventListener("click", closeMenuModal);
}

if (menuModal) {
  menuModal.addEventListener("click", (event) => {
    if (event.target === menuModal) {
      closeMenuModal();
    }
  });
}

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeMenuModal();
  }
});

function applyMenuFilter(category) {
  menuSections.forEach((section) => {
    const matches = category === "all" || section.dataset.menuCategory === category;
    section.hidden = !matches;
  });

  filterButtons.forEach((button) => {
    button.classList.toggle("is-active", button.dataset.menuFilter === category);
  });
}

filterButtons.forEach((button) => {
  button.addEventListener("click", () => {
    applyMenuFilter(button.dataset.menuFilter || "all");
  });
});
