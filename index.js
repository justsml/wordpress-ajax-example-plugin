// Here's an example of calling an AJAX endpoint in WordPress:
function getCustomer(query) {
  if (!query) throw new Error('getCustomer needs object with an email key');
  if (!query.email || query.email.length < 6) {
    throw new Error('getCustomer(query) requires a valid email property');
  }
  // action must line up with the method passed to `registerAjax` in index.php
  query.action = 'get_customer';

  return fetch(ajaxHooks.url, {
    method: 'POST',
    body: new URLSearchParams(query),
    headers: new Headers({
      'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8',
    }),
  })
  .then(response => response.json())
  .catch(error => console.error('AjaxHooks Error:', error))
}