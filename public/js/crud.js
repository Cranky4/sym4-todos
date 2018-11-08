$('body').on('.js-delete', 'click', function (e) {
  e.stopPropagation()
  e.preventDefault()

  if (confirm('Are you sure?')) {
    fetch(e.target.getAttribute('href'), {
      method: 'DELETE',
    }).then(res => window.location.replace('/'))
  }
  return false
})
