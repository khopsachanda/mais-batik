const validatePriceRange = (form) => {
  const minimum = form.elements.namedItem("price_min");
  const maximum = form.elements.namedItem("price_max");

  if (!(minimum instanceof HTMLInputElement) || !(maximum instanceof HTMLInputElement)) {
    return true;
  }

  maximum.setCustomValidity(
    Number(maximum.value) >= Number(minimum.value)
      ? ""
      : "Harga maksimum tidak boleh kurang dari harga minimum."
  );

  return form.reportValidity();
};

document.querySelectorAll(".product-form").forEach((form) => {
  form.addEventListener("input", (event) => {
    if (event.target instanceof HTMLInputElement && event.target.name === "price_min") {
      const maximum = form.elements.namedItem("price_max");
      if (maximum instanceof HTMLInputElement) {
        maximum.setCustomValidity("");
      }
    }
  });

  form.addEventListener("submit", (event) => {
    if (!validatePriceRange(form)) {
      event.preventDefault();
    }
  });
});

document.querySelectorAll(".order-form [data-product-select]").forEach((select) => {
  const updateProductDefaults = () => {
    if (!(select instanceof HTMLSelectElement)) {
      return;
    }

    const option = select.selectedOptions[0];
    const form = select.form;
    const variant = form?.querySelector("[data-order-variant]");
    const price = form?.querySelector("[data-order-price]");

    if (variant instanceof HTMLInputElement && option?.dataset.variant) {
      variant.value = option.dataset.variant;
    }

    if (price instanceof HTMLInputElement && option?.dataset.price) {
      price.value = option.dataset.price;
    }
  };

  select.addEventListener("change", updateProductDefaults);
});

const deleteDialog = document.querySelector("#delete-dialog");
const deleteForm = document.querySelector("#delete-form");
const deleteAction = document.querySelector("#delete-action");
const deleteId = document.querySelector("#delete-id");
const deleteMessage = document.querySelector("#delete-message");

if (
  deleteDialog instanceof HTMLDialogElement &&
  deleteForm instanceof HTMLFormElement &&
  deleteAction instanceof HTMLInputElement &&
  deleteId instanceof HTMLInputElement &&
  deleteMessage instanceof HTMLElement
) {
  document.querySelectorAll(".delete-request").forEach((button) => {
    button.addEventListener("click", () => {
      if (!(button instanceof HTMLButtonElement)) {
        return;
      }

      const recordId = button.dataset.recordId;
      const recordType = button.dataset.recordType;
      const recordName = button.dataset.recordName;

      if (!recordId || !recordType || !recordName) {
        return;
      }

      deleteAction.value = recordType === "produk" ? "delete_product" : "delete_order";
      deleteId.value = recordId;
      deleteMessage.textContent = `Yakin ingin menghapus ${recordType} “${recordName}”? Tindakan ini tidak dapat dibatalkan.`;
      deleteDialog.showModal();
    });
  });

  document.querySelector("[data-close-dialog]")?.addEventListener("click", () => {
    deleteDialog.close();
  });

  deleteDialog.addEventListener("click", (event) => {
    if (event.target === deleteDialog) {
      deleteDialog.close();
    }
  });
}
