function initListDisplay() {
  const handleClickEvent = (e) => {
    const target = e.target.closest('[data-toggle-listing]');

    if (target && target.dataset.toggleListing !== undefined) {
      e.preventDefault();

      if (target.classList.contains('active')) {
        return;
      }

      const display = target.dataset.displayType;
      const allButtons = document.querySelectorAll('[data-toggle-listing]');

      allButtons.forEach((button) => {
        button.classList.remove('active');
      });

      target.classList.add('active');

      let requestData = {
        displayType: display,
        ajax: 1,
      };

      requestData = Object.keys(requestData).map((key) => `${encodeURIComponent(key)}=${encodeURIComponent(requestData[key])}`).join('&');

      fetch(window.listDisplayAjaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: requestData,
      })
        .then((resp) => resp.text())
        .then((resp) => {
          try {
            const response = JSON.parse(resp);

            if (response.success) {
              prestashop.emit('updateFacets', window.location.href);
            }
          } catch (error) {
            console.error(error); // eslint-disable-line no-console
          }
        })
        .catch((error) => {
          console.error(error); // eslint-disable-line no-console
        });
    }
  };

  document.addEventListener('click', handleClickEvent);
}

document.addEventListener('DOMContentLoaded', () => {
  initListDisplay();
});
