<html>

<body>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script type="text/javascript">
            snap.pay('{{ $snap_token }}', {
                // Optional
                onSuccess: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    // window.location = `/payments/${'{{ $type }}'}/success?snap_token=${'{{ $snap_token }}'}`;
                    window.location = `/payments/${'{{ $type }}'}/${'{{ $snap_token }}'}/status?status=success`;
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    // window.location = `/payments/${'{{ $type }}'}/failed?snap_token=${'{{ $snap_token }}'}`;
                    window.location = `/payments/${'{{ $type }}'}/${'{{ $snap_token }}'}/status?status=failed`;
                }
            });
    </script>
</body>

</html>
