<!DOCTYPE html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
  <script>
    console.log('user_id'+ {{$id}} );
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('61c821012499eeec3c22', {
      cluster: 'ap1'
    });

    var channel = pusher.subscribe('my-channel-'+{{$id}});
    channel.bind('my-event', function(data) {
      console.log(data);
      alert(data.message);
    });
  </script>
</head>
<body>
  <h1>Pusher Test</h1>
  <p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
  </p>
</body>