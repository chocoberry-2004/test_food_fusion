document.querySelectorAll('.like-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.dataset.id;
    fetch('like_entry.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `entry_id=${id}`
    })
      .then(res => res.text())
      .then(likes => {
        btn.innerHTML = `👏 ${likes}`;
      });
  });
});
