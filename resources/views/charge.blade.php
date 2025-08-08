<form id="payment-form">
  <div id="card-element"><!-- A Stripe Element will be inserted here. --></div>
  <button type="submit">Submit Payment</button>
</form>

<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>

<script>
  // Initialize Stripe
  var stripe = Stripe('pk_test_51PuRLbRwo3FzZSRrdTXZuY6x7guuyPneABvcKWZfS8Dcsepn1uNT6zNFw8Xb9Xxt1rFRjOBgo5XcBNxVpuZp2FN300CxgFx28K');
  var elements = stripe.elements();

  // Create an instance of the card Element
  var card = elements.create('card');
  card.mount('#card-element');

  // Handle form submission
  var form = document.getElementById('payment-form');
  form.addEventListener('submit', function(event) {
    event.preventDefault();

    stripe.createPaymentMethod({
      type: 'card',
      card: card,
    }).then(function(result) {
      if (result.error) {
        // Display error.message in your UI
        console.error(result.error.message);
      } else {
        // Send result.paymentMethod.id to your server
        fetch('/charge', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            payment_method_id: result.paymentMethod.id,
            amount: 20000, // Amount in cents
            payment_method: 'stripe'
          }),
        }).then(function(response) {
          return response.json();
        }).then(function(paymentResult) {
          if (paymentResult.error) {
            // Display error.message in your UI
            console.error(paymentResult.error);
          } else {
            // Payment succeeded
            console.log('Payment succeeded:', paymentResult);
          }
        });
      }
    });
  });
</script>