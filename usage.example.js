
/*
To access the data from other JS code, simply use like so:
*/
function initData() {
  const resultsCountDiv = document.getElementById('div.results-count')

  // getCustomer should be included via `index.js`
  getCustomer({email: 'josh@josh.com'})
  .then(customers => {
    console.log('customers:', customers)
    // `customers` should be an array, let's display how much results we got:
    resultsCountDiv.textContent = customers.length
  })
}
