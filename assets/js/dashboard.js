(() => {
  document.querySelectorAll('.bo-dashboard').forEach((dashboard) => {
    let timer;
    dashboard.addEventListener('click', (event) => {
      if (!event.target.closest('[data-bo-preview]')) return;
      const notice = dashboard.querySelector('.bo-toast');
      notice.hidden = false;
      clearTimeout(timer);
      timer = setTimeout(() => { notice.hidden = true; }, 4200);
    });
  });
})();
