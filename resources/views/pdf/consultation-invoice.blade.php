<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" >
</head>
<body>
    {{-- Header --}}
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h2>Kebunify</h2>
            </div>
        </div>
    </div>

    <hr>


    <p>No. Invoice: {{ $consultation->transaction->payment_receipt }}</p>
    <p>Tanggal: {{ $consultation->created_at }}</p>
    <p>Nama: {{ $consultation->user->name }}</p>
    <p>Nama Pakar: {{ $consultation->expert->user->name }}</p>
    <p>Biaya Konsultasi: Rp. {{ $consultation->transaction->amount }}</p>
    <p>Status: {{ ($consultation->transaction->status == 'success') ? 'Lunas' : 'Belum Lunas' }}</p>
</body>
</html>
