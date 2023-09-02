<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<title>{{ $transaction->invoice_number }}</title>

		<!-- Favicon -->
		<!-- <link rel="icon" href="./images/favicon.png" type="image/x-icon" /> -->

		<!-- Invoice styling -->
		<style>
			body {
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				text-align: center;
				color: #777;
			}

			body h1 {
				font-weight: 300;
				margin-bottom: 0px;
				padding-bottom: 0px;
				color: #000;
			}

			body h3 {
				font-weight: 300;
				margin-top: 10px;
				margin-bottom: 20px;
				font-style: italic;
				color: #555;
			}

			body a {
				color: #06f;
			}

			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
				border-collapse: collapse;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

            .invoice-box table tr.heading td:nth-child(3) {
				text-align:right;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.details td:nth-child(3) {
				text-align:right;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
                text-align: center;
			}

			.invoice-box table tr.total td:nth-child(3) {
				border-top: 2px solid #eee;
				font-weight: bold;
                text-align:right;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}
		</style>
	</head>

	<body>
		<div class="invoice-box">
            <table>
                <tbody>
                    <tr class="top">
                        <td colspan="3">
                            <table>
                                <tr>
                                    <td class="title">
                                        <img src="data:image/svg+xml;base64,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" alt="Company logo" style="width: 100%; max-width: 100px" />
                                        <!-- syarat gambar dengan asset laravel public harus png or jpg -->
                                        <!-- <img src="{{ public_path('images/logo.png') }}" alt="Company logo" style="width: 100%; max-width: 100px" /> -->
                                    </td>

                                    <td style="vertical-align: middle; text-align:right;">
                                        <strong>Invoice #: {{ $transaction->invoice_number }}</strong><br />
                                        {{ $transaction->updated_at->format('D, M d Y') }}<br /><br />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="information">
                        <td colspan="3">
                            <table>
                                <tr>
                                    <td>
                                        <b>Alamat Pengiriman.</b><br />
                                        {{ $transaction->address }}
                                    </td>
                                    <td></td>
                                    <td style="text-align:right;">
                                        {{ $transaction->user->name }}<br />
                                        {{ $transaction->user->phone_number }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="heading">
                        <td>Detail</td>
                        <td></td>
                        <td>Check #</td>
                    </tr>

                    <tr class="details">
                        <td><b>Metode Pembayaran</b></td>
                        <td></td>
                        <td>{{ $transaction->payment }}</td>
                    </tr>

                    <tr class="details">
                        <td><b>Status Pembayaran</b></td>
                        <td></td>
                        <td>{{ $transaction->status == "PENDING" ? "Belum dibayar" : ($transaction->status == "SUCCESS" ? "Diterima" : ($transaction->status == "SHIPPING" ? "Dikemas" : "Dikirim")) }}</td>
                    </tr>

                    <tr class="details">
                        <td><b>Biaya Pengiriman</b></td>
                        <td></td>
                        <td>
                            @if($transaction->shipping_price != 0)
                                number_format($transaction->shipping_price)
                            @else
                                Free
                            @endif
                        </td>
                    </tr>

                    <tr class="heading">
                        <td>Nama Produk</td>
                        <td style="text-align:center;">Quantity</td>
                        <td>Harga</td>
                    </tr>
                    @foreach($transaction->items as $item)
                        <tr class="item">
                            <!-- <td class="whitespace-nowrap px-6 py-4 font-medium">1</td> -->
                            <td>{{ $item->product->name }}</td>
                            <td style="text-align:center;">{{ $item->quantity }}</td>
                            <td style="text-align:right;">Rp.{{ number_format($item->product->price) }}</td>
                        </tr>
                    @endforeach

                    <tr class="total">
                        <td></td>
                        <td></td>

                        <td>Total Bayar: Rp{{ number_format($transaction->total_price) }}</td>
                    </tr>
                </tbody>
			</table>
		</div>
	</body>
</html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
