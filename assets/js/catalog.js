(() => {
  document.querySelectorAll('.bo-catalog-app').forEach((app) => {
    const photoField = app.querySelector('.bo-live-photo-picker > .bo-field-wide');
    if (photoField && photoField.tagName !== 'FIELDSET') {
      const fieldset = document.createElement('fieldset');
      fieldset.className = photoField.className + ' bo-product-photo-field';
      const legend = document.createElement('legend');
      legend.textContent = photoField.querySelector('.bo-field-label')?.textContent || 'Product photo';
      fieldset.appendChild(legend);
      Array.from(photoField.childNodes).forEach((node) => {
        if (!node.matches || !node.matches('.bo-field-label')) fieldset.appendChild(node);
      });
      photoField.replaceWith(fieldset);
    }
    const baseField = app.querySelector('.bo-base-price-field');
    if (baseField && baseField.tagName !== 'FIELDSET') {
      const fieldset = document.createElement('fieldset');
      fieldset.className = baseField.className;
      const legend = document.createElement('legend');
      legend.textContent = 'Base price';
      fieldset.appendChild(legend);
      Array.from(baseField.childNodes).forEach((node) => {
        if (node.nodeType !== Node.TEXT_NODE && !(node.matches && node.matches('.bo-field-hint'))) fieldset.appendChild(node);
      });
      baseField.replaceWith(fieldset);
    }
    app.querySelectorAll('.bo-product-form legend').forEach((legend) => legend.classList.add('bo-form-section-header'));
    const availabilityField = app.querySelector('.bo-product-weekdays');
    if (availabilityField && !availabilityField.querySelector('[data-bo-product-schedule]')) {
      const schedule = window.bakeryOrderingProductSchedule || {};
      const scheduleRow = document.createElement('div');
      scheduleRow.className = 'bo-product-schedule';
      scheduleRow.dataset.boProductSchedule = '';
      const scheduleHeadingRow = document.createElement('div');
      scheduleHeadingRow.className = 'bo-product-schedule-heading-row';
      const scheduleHeading = document.createElement('strong');
      scheduleHeading.className = 'bo-product-schedule-heading';
      scheduleHeading.textContent = 'Schedule Product (Optional)';
      const recurringLabel = document.createElement('label');
      recurringLabel.className = 'bo-product-schedule-recurring';
      const recurringInput = document.createElement('input');
      recurringInput.type = 'checkbox';
      recurringInput.name = 'product_schedule_recurring';
      recurringInput.checked = !!schedule.recurring;
      recurringLabel.append(recurringInput, document.createTextNode('Recurring'));
      scheduleHeadingRow.append(scheduleHeading, recurringLabel);
      scheduleRow.appendChild(scheduleHeadingRow);
      [['product_start_date', 'Start date', schedule.start || ''], ['product_end_date', 'End date', schedule.end || '']].forEach(([name, labelText, value]) => {
        const label = document.createElement('label');
        label.textContent = labelText;
        const input = document.createElement('input');
        input.type = 'date';
        input.name = name;
        input.value = value;
        input.classList.toggle('is-empty', !value);
        input.addEventListener('input', () => input.classList.toggle('is-empty', !input.value));
        label.appendChild(input);
        scheduleRow.appendChild(label);
      });
      const scheduleHint = document.createElement('p');
      scheduleHint.className = 'bo-product-schedule-hint';
      scheduleHint.textContent = 'Leave both dates blank for year-round availability.';
      scheduleRow.appendChild(scheduleHint);
      availabilityField.appendChild(scheduleRow);
    }
    const pricingBaseField = app.querySelector('.bo-base-price-field');
    if (pricingBaseField && !pricingBaseField.querySelector('[data-bo-attached-pricing-groups]')) {
      const pricingSpot = document.createElement('div');
      pricingSpot.className = 'bo-attached-pricing-groups';
      pricingSpot.dataset.boAttachedPricingGroups = '';
      const groupLabels = Array.from(app.querySelectorAll('.bo-price-grid > label'));
      if (groupLabels.length) {
        const heading = document.createElement('strong');
        heading.textContent = 'Pricing groups';
        pricingSpot.appendChild(heading);
        groupLabels.forEach((label) => {
          const row = document.createElement('div');
          row.className = 'bo-attached-pricing-group';
          const name = document.createElement('span');
          name.textContent = Array.from(label.childNodes).find((node) => node.nodeType === Node.TEXT_NODE)?.textContent.trim() || 'Pricing group';
          const price = document.createElement('span');
          price.className = 'bo-attached-pricing-value';
          const input = label.querySelector('input');
          const updatePrice = () => { price.textContent = input && input.value !== '' ? '$' + Number(input.value).toFixed(2) : '—'; };
          updatePrice();
          input?.addEventListener('input', updatePrice);
          row.append(name, price);
          pricingSpot.appendChild(row);
        });
      } else {
        const emptyMessage = document.createElement('div');
        emptyMessage.textContent = 'No pricing groups attached.';
        pricingSpot.appendChild(emptyMessage);
      }
      const addPricingGroup = document.createElement('button');
      addPricingGroup.type = 'button';
      addPricingGroup.className = 'bo-card-add-category bo-add-pricing-group';
      addPricingGroup.textContent = '+ Pricing Group';
      const pricingPicker = document.createElement('div');
      pricingPicker.className = 'bo-pricing-group-picker';
      pricingPicker.hidden = true;
      pricingPicker.textContent = groupLabels.length ? 'Choose a pricing group.' : 'No pricing groups available.';
      addPricingGroup.addEventListener('click', () => { pricingPicker.hidden = !pricingPicker.hidden; });
      pricingSpot.append(addPricingGroup, pricingPicker);
      pricingBaseField.appendChild(pricingSpot);
    }
    const selectedCategories = app.querySelector('[data-bo-selected-categories]');
    const categorySearch = app.querySelector('[data-bo-category-search]');
    const categoryResults = app.querySelector('[data-bo-category-results]');
    const addSelectedCategory = (name, id, isNew = false) => {
      if (!selectedCategories) return false;
      const key = isNew ? name.toLowerCase() : String(id);
      const exists = Array.from(selectedCategories.querySelectorAll('input')).some((input) => String(input.value).toLowerCase() === key);
      if (exists) return false;
      const label = document.createElement('label');
      label.className = 'bo-selected-category';
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.checked = true;
      checkbox.name = isNew ? 'new_product_categories[]' : 'product_categories[]';
      checkbox.value = isNew ? name : id;
      const text = document.createElement('span');
      text.textContent = name;
      const remove = document.createElement('button');
      remove.type = 'button';
      remove.textContent = '×';
      remove.setAttribute('aria-label', 'Remove ' + name);
      remove.dataset.boRemoveSelectedCategory = '';
      label.append(checkbox, text, remove);
      selectedCategories.append(label);
      return true;
    };
    const refreshCategoryResults = () => {
      if (!categorySearch || !categoryResults) return;
      const query = categorySearch.value.trim().toLowerCase();
      const selected = new Set(Array.from(selectedCategories?.querySelectorAll('input[name="product_categories[]"]') || []).map((input) => String(input.value)));
      let visible = 0;
      categoryResults.querySelectorAll('[data-bo-select-category]').forEach((option) => {
        const matches = option.dataset.categoryName.toLowerCase().includes(query) && !selected.has(String(option.dataset.categoryId));
        option.hidden = !matches;
        if (matches) visible++;
      });
      const empty = categoryResults.querySelector('[data-bo-category-no-results]');
      if (empty) empty.hidden = visible > 0;
    };
    app.addEventListener('click', (event) => {
      const open = event.target.closest('[data-bo-open-form]');
      if (open) {
        const selector = open.dataset.boOpenForm === 'group' ? '.bo-group-form-wrap' : '.bo-product-form-wrap';
        const form = app.querySelector(selector);
        if (form) {
          form.hidden = false;
          if (open.dataset.boOpenForm === 'product') {
            const preview = app.querySelector('.bo-live-preview-wrap');
            if (preview) preview.hidden = false;
          }
          form.scrollIntoView({ behavior: 'smooth', block: 'start' });
          const field = form.querySelector('input:not([type=hidden]):not([type=file])');
          if (field) field.focus({ preventScroll: true });
        }
      }
      const categoryOption = event.target.closest('[data-bo-select-category]');
      if (categoryOption) {
        addSelectedCategory(categoryOption.dataset.categoryName, categoryOption.dataset.categoryId);
        if (categorySearch) {
          categorySearch.value = '';
          categorySearch.setAttribute('aria-expanded', 'false');
        }
        if (categoryResults) categoryResults.hidden = true;
        refreshCategoryResults();
      }
      const removeSelectedCategory = event.target.closest('[data-bo-remove-selected-category]');
      if (removeSelectedCategory) {
        removeSelectedCategory.closest('.bo-selected-category')?.remove();
        refreshCategoryResults();
      }
      const addCategoryButton = event.target.closest('[data-bo-add-category]');
      if (addCategoryButton) {
        const row = app.querySelector('[data-bo-new-category-row]');
        if (row) {
          row.hidden = false;
          row.querySelector('[data-bo-new-category]')?.focus();
        }
      }
      const confirmCategoryButton = event.target.closest('[data-bo-confirm-category]');
      if (confirmCategoryButton) {
        const row = confirmCategoryButton.closest('[data-bo-new-category-row]');
        const input = row?.querySelector('[data-bo-new-category]');
        const name = input ? input.value.trim().slice(0, 80) : '';
        if (!name || !selectedCategories) {
          input?.focus();
          return;
        }
        const existingOption = Array.from(categoryResults?.querySelectorAll('[data-bo-select-category]') || []).find((option) => option.dataset.categoryName.toLowerCase() === name.toLowerCase());
        const added = existingOption
          ? addSelectedCategory(existingOption.dataset.categoryName, existingOption.dataset.categoryId)
          : addSelectedCategory(name, '', true);
        if (!added) {
          input.setCustomValidity('This category is already selected.');
          input.reportValidity();
          return;
        }
        input.setCustomValidity('');
        input.value = '';
        row.hidden = true;
        refreshCategoryResults();
      }
      const addUnitButton = event.target.closest('[data-bo-add-unit]');
      if (addUnitButton) {
        const row = app.querySelector('[data-bo-new-unit-row]');
        if (row) {
          row.hidden = false;
          row.querySelector('[data-bo-new-unit]')?.focus();
        }
      }
      const confirmUnitButton = event.target.closest('[data-bo-confirm-unit]');
      if (confirmUnitButton) {
        const row = confirmUnitButton.closest('[data-bo-new-unit-row]');
        const input = row?.querySelector('[data-bo-new-unit]');
        const select = app.querySelector('[data-bo-unit]');
        const unit = input ? input.value.trim().slice(0, 40) : '';
        if (!unit || !select) {
          input?.focus();
          return;
        }
        let option = Array.from(select.options).find((item) => item.value.toLowerCase() === unit.toLowerCase());
        if (!option) {
          option = new Option(unit, unit);
          select.add(option);
        }
        select.value = option.value;
        input.value = '';
        row.hidden = true;
        updatePreview();
      }
      const removeImageButton = event.target.closest('[data-bo-remove-image]');
      if (removeImageButton) {
        if (removeImageButton.disabled) return;
        if (!window.confirm('Remove this product photo? The change will take effect when you save the product.')) return;
        const flag = app.querySelector('[data-bo-remove-image-flag]');
        if (flag) flag.value = '1';
        removeImageButton.disabled = true;
        const pickerPreview = app.querySelector('.bo-upload-preview');
        if (pickerPreview) {
          const icon = pickerPreview.querySelector('.bo-upload-placeholder-overlay');
          if (icon) icon.hidden = false;
          pickerPreview.querySelectorAll('img').forEach((image) => image.remove());
        }
        const status = app.querySelector('[data-bo-image-status]');
        if (status) status.textContent = 'Photo will be removed when you save.';
        const liveImage = app.querySelector('[data-preview-image]');
        if (liveImage) liveImage.src = liveImage.dataset.defaultSrc || '';
      }
      const editCardCategoryButton = event.target.closest('[data-bo-edit-card-category]');
      if (editCardCategoryButton) {
        const card = editCardCategoryButton.closest('.bo-product-card');
        const popover = card?.querySelector('[data-bo-card-category-edit-popover]');
        const shouldOpen = popover?.hidden;
        app.querySelectorAll('[data-bo-card-category-popover], [data-bo-card-category-edit-popover]').forEach((panel) => { panel.hidden = true; });
        if (popover) {
          popover.hidden = !shouldOpen;
          const idInput = popover.querySelector('[data-bo-edit-category-id]');
          const nameInput = popover.querySelector('[data-bo-edit-category-name]');
          if (idInput) idInput.value = editCardCategoryButton.dataset.categoryId || '';
          if (nameInput) {
            nameInput.value = editCardCategoryButton.dataset.categoryName || '';
            if (shouldOpen) nameInput.focus();
          }
        }
      }
      const cardCategoryButton = event.target.closest('[data-bo-open-card-category]');
      if (cardCategoryButton) {
        const card = cardCategoryButton.closest('.bo-product-card');
        const popover = card?.querySelector('[data-bo-card-category-popover]');
        const shouldOpen = popover?.hidden;
        app.querySelectorAll('[data-bo-card-category-popover], [data-bo-card-category-edit-popover]').forEach((panel) => { panel.hidden = true; });
        if (popover) {
          popover.hidden = !shouldOpen;
          if (shouldOpen) popover.querySelector('select, input')?.focus();
        }
      }
      const menuButton = event.target.closest('[data-bo-menu]');
      app.querySelectorAll('.bo-product-menu').forEach((menu) => {
        if (!menuButton || menu !== menuButton.parentElement.querySelector('.bo-product-menu')) menu.hidden = true;
      });
      if (menuButton) {
        const menu = menuButton.parentElement.querySelector('.bo-product-menu');
        menu.hidden = !menu.hidden;
      }
      if (event.target.matches('[data-bo-image]')) return;
    });
    if (categorySearch && categoryResults) {
      categorySearch.addEventListener('focus', () => {
        refreshCategoryResults();
        categoryResults.hidden = false;
        categorySearch.setAttribute('aria-expanded', 'true');
      });
      categorySearch.addEventListener('input', () => {
        refreshCategoryResults();
        categoryResults.hidden = false;
        categorySearch.setAttribute('aria-expanded', 'true');
      });
      categorySearch.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          categoryResults.hidden = true;
          categorySearch.setAttribute('aria-expanded', 'false');
        }
      });
      document.addEventListener('click', (event) => {
        if (!event.target.closest('.bo-category-picker')) {
          categoryResults.hidden = true;
          categorySearch.setAttribute('aria-expanded', 'false');
        }
      });
    }
    const livePreview = app.querySelector('.bo-live-preview-wrap');
    const updatePreview = () => {
      const productForm = app.querySelector('.bo-product-form');
      if (!productForm || !livePreview) return;
      const value = (name) => {
        const element = productForm.querySelector('[name="' + name + '"]');
        return element ? element.value : '';
      };
      const productName = value('product_name').trim();
      const unitName = value('item_unit').trim() || 'Each';
      const quantity = Math.max(1, parseInt(value('order_increment'), 10) || 1);
      const previewName = livePreview.querySelector('[data-preview-name]');
      if (previewName) previewName.textContent = productName || 'Product name';
      const description = livePreview.querySelector('[data-preview-description]');
      if (description) description.textContent = value('description').trim() || 'Product description will appear here.';
      const rule = 'Sold ' + unitName.toLowerCase() + ' · multiples of ' + quantity;
      livePreview.querySelectorAll('[data-preview-unit], [data-preview-rule]').forEach((node) => { node.textContent = rule; });
      const unitSuffixes = { each: 'ea', dozen: 'doz', pound: 'lb' };
      const unitSuffix = unitSuffixes[unitName.toLowerCase()] || unitName.toLowerCase();
      const baseMoney = productForm.querySelector('.bo-base-price-field .bo-money-input');
      if (baseMoney) {
        let baseUnit = baseMoney.querySelector('[data-bo-base-price-unit]');
        if (!baseUnit) {
          baseUnit = document.createElement('span');
          baseUnit.className = 'bo-money-unit-suffix';
          baseUnit.setAttribute('data-bo-base-price-unit', '');
          baseMoney.appendChild(baseUnit);
        }
        baseUnit.textContent = unitName.toLowerCase();
      }
      livePreview.querySelectorAll('[data-preview-price-unit]').forEach((node) => { node.textContent = ' / ' + unitSuffix; });
      const baseValue = value('base_price');
      const base = livePreview.querySelector('[data-preview-base]');
      if (base) base.textContent = baseValue !== '' ? '$' + Number(baseValue).toFixed(2) : '—';
      const baseSuffix = base?.parentElement.querySelector('[data-preview-price-unit]');
      if (baseSuffix) baseSuffix.hidden = baseValue === '';
      const weekdayNames = { monday: 'Monday', tuesday: 'Tuesday', wednesday: 'Wednesday', thursday: 'Thursday', friday: 'Friday', saturday: 'Saturday', sunday: 'Sunday' };
      const selectedWeekdays = Array.from(productForm.querySelectorAll('[name="available_weekdays[]"]:checked')).map((input) => weekdayNames[input.value]).filter(Boolean);
      const availability = livePreview.querySelector('[data-preview-availability]');
      if (availability) availability.textContent = selectedWeekdays.length === 7 ? 'Available every day' : (selectedWeekdays.length ? 'Available ' + selectedWeekdays.join(', ') : 'No weekdays selected');
      productForm.querySelectorAll('[name^="group_prices["]').forEach((input) => {
        const match = input.name.match(/^group_prices\[(\d+)\]$/);
        if (!match) return;
        const output = livePreview.querySelector('[data-preview-group="' + match[1] + '"]');
        if (output) output.textContent = input.value !== '' ? '$' + Number(input.value).toFixed(2) : '—';
        const suffix = output?.parentElement.querySelector('[data-preview-price-unit]');
        if (suffix) suffix.hidden = input.value === '';
      });
    };
    app.addEventListener('input', (event) => {
      if (event.target.closest('.bo-product-form')) updatePreview();
    });
    app.addEventListener('change', (event) => {
      if (event.target.closest('.bo-product-form')) updatePreview();
    });
    updatePreview();
    app.addEventListener('change', (event) => {
      if (!event.target.matches('[data-bo-image]') || !event.target.files || !event.target.files[0]) return;
      const file = event.target.files[0];
      if (!file.type.startsWith('image/')) return;
      const preview = app.querySelector('.bo-upload-preview');
      const removeFlag = app.querySelector('[data-bo-remove-image-flag]');
      if (removeFlag) removeFlag.value = '0';
      const removeButton = app.querySelector('[data-bo-remove-image]');
      if (removeButton) removeButton.disabled = false;
      const status = app.querySelector('[data-bo-image-status]');
      if (status) status.textContent = '';
      if (preview) {
        const image = document.createElement('img');
        image.alt = 'Selected product photo preview';
        image.src = URL.createObjectURL(file);
        preview.querySelectorAll('img').forEach((oldImage) => oldImage.remove());
        const emptyIcon = preview.querySelector('.bo-upload-placeholder-overlay');
        if (emptyIcon) emptyIcon.hidden = true;
        preview.append(image);
        const liveImage = app.querySelector('[data-preview-image]');
        if (liveImage) liveImage.src = image.src;
      }
    });
    app.querySelectorAll('[data-bo-card-category-edit-popover]').forEach((form) => {
      form.addEventListener('submit', (event) => {
        if (event.submitter?.value === 'remove_from_product' && !window.confirm('Remove this category from the product?')) event.preventDefault();
      });
    });
    app.querySelectorAll('[data-bo-confirm]').forEach((form) => {
      form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.boConfirm)) event.preventDefault();
      });
    });
    const groupForm = app.querySelector('.bo-group-form-wrap:not([hidden])');
    if (groupForm) groupForm.scrollIntoView({ block: 'start' });
    const productForm = app.querySelector('.bo-product-form-wrap:not([hidden])');
    if (productForm) productForm.scrollIntoView({ block: 'start' });
  });
})();
