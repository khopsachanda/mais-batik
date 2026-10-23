const menuToggle = document.querySelector(".menu-toggle");
const navigation = document.querySelector("#primary-navigation");

if (menuToggle instanceof HTMLButtonElement && navigation instanceof HTMLElement) {
  const closeMenu = () => {
    menuToggle.setAttribute("aria-expanded", "false");
    menuToggle.setAttribute("aria-label", "Buka navigasi");
    navigation.classList.remove("is-open");
  };

  menuToggle.addEventListener("click", () => {
    const isExpanded = menuToggle.getAttribute("aria-expanded") === "true";
    menuToggle.setAttribute("aria-expanded", String(!isExpanded));
    menuToggle.setAttribute("aria-label", isExpanded ? "Buka navigasi" : "Tutup navigasi");
    navigation.classList.toggle("is-open", !isExpanded);
  });

  navigation.addEventListener("click", (event) => {
    if (event.target instanceof HTMLAnchorElement) {
      closeMenu();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && menuToggle.getAttribute("aria-expanded") === "true") {
      closeMenu();
      menuToggle.focus();
    }
  });
}

const currentYear = document.querySelector("#current-year");

if (currentYear) {
  currentYear.textContent = String(new Date().getFullYear());
}

const productSearch = document.querySelector("#product-search");
const categoryFilter = document.querySelector("#category-filter");
const productCards = [...document.querySelectorAll(".product-card")];
const productCount = document.querySelector("#product-count");
const catalogEmpty = document.querySelector("#catalog-empty");

const filterProducts = () => {
  if (!(productSearch instanceof HTMLInputElement) || !(categoryFilter instanceof HTMLSelectElement)) {
    return;
  }

  const query = productSearch.value.trim().toLocaleLowerCase("id");
  const category = categoryFilter.value;
  let visibleCount = 0;

  for (const card of productCards) {
    const matchesCategory = category === "semua" || card.dataset.category === category;
    const matchesSearch = (card.dataset.search ?? "").toLocaleLowerCase("id").includes(query);
    const isVisible = matchesCategory && matchesSearch;

    card.hidden = !isVisible;
    visibleCount += Number(isVisible);
  }

  if (productCount) {
    productCount.textContent = `Menampilkan ${visibleCount} produk`;
  }

  if (catalogEmpty instanceof HTMLElement) {
    catalogEmpty.hidden = visibleCount > 0;
  }
};

productSearch?.addEventListener("input", filterProducts);
categoryFilter?.addEventListener("change", filterProducts);

const productDialog = document.querySelector("#product-dialog");
const dialogTitle = document.querySelector("#dialog-title");
const dialogDescription = document.querySelector("#dialog-description");
const dialogOrder = document.querySelector("#dialog-order");

if (
  productDialog instanceof HTMLDialogElement &&
  dialogTitle instanceof HTMLElement &&
  dialogDescription instanceof HTMLElement &&
  dialogOrder instanceof HTMLAnchorElement
) {
  document.querySelectorAll(".product-detail").forEach((button) => {
    button.addEventListener("click", () => {
      const productName = button.dataset.product;
      const description = button.dataset.description;
      const productCard = button.closest(".product-card");
      const orderLink = productCard?.querySelector(".product-order");

      if (!productName || !description || !(productCard instanceof HTMLElement)) {
        return;
      }

      dialogTitle.textContent = productName;
      dialogDescription.textContent = description;
      if (orderLink instanceof HTMLAnchorElement) {
        dialogOrder.href = orderLink.href;
        dialogOrder.removeAttribute("aria-disabled");
        dialogOrder.classList.remove("is-disabled");
        dialogOrder.textContent = "Tanyakan produk";
      } else {
        dialogOrder.removeAttribute("href");
        dialogOrder.setAttribute("aria-disabled", "true");
        dialogOrder.classList.add("is-disabled");
        dialogOrder.textContent = "Stok habis";
      }
      productDialog.showModal();
    });
  });

  productDialog.addEventListener("click", (event) => {
    if (event.target === productDialog) {
      productDialog.close();
    }
  });

  productDialog.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      event.preventDefault();
      productDialog.close();
    }
  });
}
