const todos = document.getElementsByClassName('js-delete')

for (var i = 0; i < todos.length; i++) {
  todos[i].addEventListener('click', (e) => {
    e.stopPropagation()
    e.preventDefault()
    if (confirm('Are you sure?')) {
      fetch(e.target.getAttribute('href'), {
        method: 'DELETE',
      }).then(res => window.location.replace('/'))
    }
    return false
  })
}
