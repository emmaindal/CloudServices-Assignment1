let id = document.getElementById('unicorn-id')
document.getElementById('show-unicorn').disabled = true

id.addEventListener('input', function(id) {
  let value = document.getElementById('unicorn-id').value
  if (!value) {
    document.getElementById('show-unicorn').disabled = true
  } else {
    document.getElementById('show-unicorn').disabled = false
  }
})
